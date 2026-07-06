<?php

class ProgressService
{
    public function __construct(
        private readonly ClickUpClient $client,
        private readonly ?string $teamId,
    ) {}

    public function getSpaceProgress(): array
    {
        $teamId = $this->resolveTeamId();
        $spaces = $this->client->getSpaces($teamId);
        $tasks = $this->client->getAllTeamTasks($teamId);

        $tasksBySpace = [];
        foreach ($tasks as $task) {
            $spaceId = $task['space']['id'] ?? null;
            if ($spaceId === null) {
                continue;
            }
            $tasksBySpace[$spaceId][] = $task;
        }

        $results = [];
        foreach ($spaces as $space) {
            if (!empty($space['archived'])) {
                continue;
            }

            $spaceId = (string) $space['id'];
            $spaceTasks = $tasksBySpace[$spaceId] ?? [];
            $metrics = $this->calculateMetrics($spaceTasks);

            $results[] = [
                'space_id' => $spaceId,
                'name' => $space['name'] ?? 'Unknown',
                'color' => $space['color'] ?? '#7B68EE',
                'avatar' => $this->normalizeAvatar($space['avatar'] ?? null),
                'initials' => $this->makeInitials($space['name'] ?? ''),
                'progress' => $metrics['progress'],
                'total_estimate_ms' => $metrics['total_estimate_ms'],
                'total_spent_ms' => $metrics['total_spent_ms'],
                'task_count' => count($spaceTasks),
                'estimated_task_count' => $metrics['estimated_task_count'],
                'has_estimate' => $metrics['has_estimate'],
            ];
        }

        usort($results, fn(array $a, array $b) => strcasecmp($a['name'], $b['name']));

        return $results;
    }

    private function resolveTeamId(): string
    {
        if ($this->teamId !== null && $this->teamId !== '') {
            return (string) $this->teamId;
        }

        $teams = $this->client->getTeams();
        if (empty($teams)) {
            throw new RuntimeException('No ClickUp workspace found for this token');
        }

        return (string) $teams[0]['id'];
    }

    private function calculateMetrics(array $tasks): array
    {
        $totalEstimate = 0;
        $totalSpent = 0;
        $estimatedTaskCount = 0;

        foreach ($tasks as $task) {
            $estimate = $this->toInt($task['time_estimate'] ?? null);
            if ($estimate <= 0) {
                continue;
            }

            $estimatedTaskCount++;
            $totalEstimate += $estimate;
            $totalSpent += $this->toInt($task['time_spent'] ?? null);
        }

        $hasEstimate = $totalEstimate > 0;
        $progress = $hasEstimate ? ($totalSpent / $totalEstimate) * 100 : 0.0;

        return [
            'progress' => round($progress, 1),
            'total_estimate_ms' => $totalEstimate,
            'total_spent_ms' => $totalSpent,
            'estimated_task_count' => $estimatedTaskCount,
            'has_estimate' => $hasEstimate,
        ];
    }

    private function toInt(mixed $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        return (int) $value;
    }

    private function normalizeAvatar(?string $avatar): ?string
    {
        if ($avatar === null || trim($avatar) === '') {
            return null;
        }

        return $avatar;
    }

    private function makeInitials(string $name): string
    {
        $name = trim($name);
        if ($name === '') {
            return '??';
        }

        $words = preg_split('/\s+/', $name) ?: [];
        if (count($words) >= 2) {
            return mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
        }

        return mb_strtoupper(mb_substr($name, 0, 2));
    }
}
