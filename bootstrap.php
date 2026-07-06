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
            return $cached;
        }
    }

    $client = new ClickUpClient($config['clickup_token']);
    $service = new ProgressService($client, $config['team_id'] ?? null);
    $spaces = $service->getSpaceProgress();

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

function formatPercent(float $progress, bool $hasEstimate): string
{
    if (!$hasEstimate) {
        return 'بدون برآورد';
    }

    return number_format($progress, 1) . '%';
}

function progressBarWidth(float $progress, bool $hasEstimate): float
{
    if (!$hasEstimate) {
        return 0;
    }

    return min(100, max(0, $progress));
}
