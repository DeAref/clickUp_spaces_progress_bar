<?php

class ClickUpClient
{
    private const BASE_URL = 'https://api.clickup.com/api/v2';
    private const MAX_RETRIES = 3;

    public function __construct(
        private readonly string $token,
    ) {}

    public function getTeams(): array
    {
        $response = $this->request('GET', '/team');
        return $response['teams'] ?? [];
    }

    public function getSpaces(string $teamId): array
    {
        $response = $this->request('GET', "/team/{$teamId}/space");
        return $response['spaces'] ?? [];
    }

    public function getAllTeamTasks(string $teamId): array
    {
        $allTasks = [];
        $page = 0;

        do {
            $query = http_build_query([
                'page' => $page,
                'include_closed' => 'true',
                'subtasks' => 'true',
            ]);

            $response = $this->request('GET', "/team/{$teamId}/task?{$query}");
            $tasks = $response['tasks'] ?? [];
            $allTasks = array_merge($allTasks, $tasks);

            $isLastPage = $response['last_page'] ?? (count($tasks) < 100);
            $page++;
        } while ($isLastPage === false);

        return $allTasks;
    }

    private function request(string $method, string $path): array
    {
        $url = self::BASE_URL . $path;
        $attempt = 0;

        while ($attempt < self::MAX_RETRIES) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $this->token,
                    'Content-Type: application/json',
                ],
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_TIMEOUT => 60,
            ]);

            $body = curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($body === false) {
                throw new RuntimeException('cURL error: ' . $error);
            }

            if ($httpCode === 429) {
                $attempt++;
                sleep(min(2 ** $attempt, 10));
                continue;
            }

            $data = json_decode($body, true);

            if ($httpCode >= 400) {
                $message = $data['err'] ?? $data['ECODE'] ?? "HTTP {$httpCode}";
                throw new RuntimeException('ClickUp API error: ' . $message);
            }

            return is_array($data) ? $data : [];
        }

        throw new RuntimeException('ClickUp API rate limit exceeded after retries');
    }
}
