<?php

function loadAppConfig(): array
{
    $configPath = __DIR__ . '/config.php';
    if (!is_file($configPath)) {
        throw new RuntimeException('config.php not found. Copy config.example.php to config.php and set your ClickUp token.');
    }

    $config = require $configPath;
    if (!is_array($config)) {
        throw new RuntimeException('config.php must return an array');
    }

    if (empty($config['clickup_token']) || $config['clickup_token'] === 'pk_YOUR_TOKEN_HERE') {
        throw new RuntimeException('Set your ClickUp API token in config.php');
    }

    return $config;
}

function loadClasses(): void
{
    require_once __DIR__ . '/src/ClickUpClient.php';
    require_once __DIR__ . '/src/FileCache.php';
    require_once __DIR__ . '/src/ProgressService.php';
}

function getProgressData(bool $forceRefresh = false): array
{
    $config = loadAppConfig();
    loadClasses();

    $cache = new FileCache(
        __DIR__ . '/cache',
        (int) ($config['cache_ttl'] ?? 300),
    );

    $cacheKey = 'space_progress_' . ($config['team_id'] ?? 'default');

    if (!$forceRefresh) {
        $cached = $cache->get($cacheKey);
        if ($cached !== null) {
            $cached['spaces'] = array_map('enrichSpaceProgress', $cached['spaces'] ?? []);

            return $cached;
        }
    }

    $client = new ClickUpClient($config['clickup_token']);
    $service = new ProgressService($client, $config['team_id'] ?? null);
    $spaces = array_map('enrichSpaceProgress', $service->getSpaceProgress());

    $payload = [
        'updated_at' => date('c'),
        'spaces' => $spaces,
    ];

    $cache->set($cacheKey, $payload);

    return $payload;
}

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function enrichSpaceProgress(array $space): array
{
    if (!isset($space['remaining_ms'])) {
        $space['remaining_ms'] = max(
            0,
            (int) ($space['total_estimate_ms'] ?? 0) - (int) ($space['total_spent_ms'] ?? 0),
        );
    }

    $hasEstimate = (bool) ($space['has_estimate'] ?? false);
    $progress = (float) ($space['progress'] ?? 0);

    $space['remaining_label'] = formatRemainingTime(
        (int) $space['remaining_ms'],
        $hasEstimate,
    );
    $space['estimate_label'] = formatDuration((int) ($space['total_estimate_ms'] ?? 0));
    $space['spent_label'] = formatDuration((int) ($space['total_spent_ms'] ?? 0));
    $space['status'] = $hasEstimate
        ? ($progress > 100 ? 'over_budget' : 'on_track')
        : 'no_estimate';

    return $space;
}

function formatDuration(int $ms): string
{
    if ($ms <= 0) {
        return '۰';
    }

    $totalMinutes = (int) round($ms / 60000);

    if ($totalMinutes < 60) {
        return (string) $totalMinutes . ' دقیقه';
    }

    $hours = intdiv($totalMinutes, 60);
    $minutes = $totalMinutes % 60;

    if ($hours < 24) {
        if ($minutes === 0) {
            return (string) $hours . ' ساعت';
        }

        return $hours . ' ساعت و ' . $minutes . ' دقیقه';
    }

    $days = intdiv($hours, 24);
    $hours = $hours % 24;

    if ($hours === 0) {
        return (string) $days . ' روز';
    }

    return $days . ' روز و ' . $hours . ' ساعت';
}

function computeAnalyticsSummary(array $spaces): array
{
    $totalEstimate = 0;
    $totalSpent = 0;
    $withEstimate = 0;
    $onTrack = 0;
    $overBudget = 0;
    $noEstimate = 0;

    foreach ($spaces as $space) {
        if (!empty($space['has_estimate'])) {
            $withEstimate++;
            $totalEstimate += (int) ($space['total_estimate_ms'] ?? 0);
            $totalSpent += (int) ($space['total_spent_ms'] ?? 0);

            if (($space['status'] ?? '') === 'over_budget') {
                $overBudget++;
            } else {
                $onTrack++;
            }
        } else {
            $noEstimate++;
        }
    }

    $overallProgress = $totalEstimate > 0
        ? round(($totalSpent / $totalEstimate) * 100, 1)
        : 0.0;

    return [
        'space_count' => count($spaces),
        'with_estimate' => $withEstimate,
        'on_track' => $onTrack,
        'over_budget' => $overBudget,
        'no_estimate' => $noEstimate,
        'total_estimate_ms' => $totalEstimate,
        'total_spent_ms' => $totalSpent,
        'remaining_ms' => max(0, $totalEstimate - $totalSpent),
        'overall_progress' => $overallProgress,
        'total_estimate_label' => formatDuration($totalEstimate),
        'total_spent_label' => formatDuration($totalSpent),
        'remaining_label' => formatRemainingTime(max(0, $totalEstimate - $totalSpent), $totalEstimate > 0),
    ];
}

function formatPercent(float $progress, bool $hasEstimate): string
{
    if (!$hasEstimate) {
        return 'بدون برآورد';
    }

    return number_format($progress, 1) . '%';
}

function formatRemainingTime(int $remainingMs, bool $hasEstimate): ?string
{
    if (!$hasEstimate || $remainingMs <= 0) {
        return null;
    }

    $totalMinutes = (int) ceil($remainingMs / 60000);

    if ($totalMinutes < 60) {
        return $totalMinutes . ' دقیقه مانده';
    }

    $hours = intdiv($totalMinutes, 60);
    $minutes = $totalMinutes % 60;

    if ($hours < 24) {
        if ($minutes === 0) {
            return $hours . ' ساعت مانده';
        }

        return $hours . ' ساعت و ' . $minutes . ' دقیقه مانده';
    }

    $days = intdiv($hours, 24);
    $hours = $hours % 24;

    if ($hours === 0) {
        return $days . ' روز مانده';
    }

    return $days . ' روز و ' . $hours . ' ساعت مانده';
}

function progressBarWidth(float $progress, bool $hasEstimate): float
{
    if (!$hasEstimate) {
        return 0;
    }

    return min(100, max(0, $progress));
}
