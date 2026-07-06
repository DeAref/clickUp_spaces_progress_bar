<?php

class FileCache
{
    public function __construct(
        private readonly string $cacheDir,
        private readonly int $ttl,
    ) {}

    public function get(string $key): ?array
    {
        $path = $this->pathFor($key);

        if (!is_file($path)) {
            return null;
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            return null;
        }

        $data = json_decode($raw, true);
        if (!is_array($data) || !isset($data['expires_at'], $data['payload'])) {
            return null;
        }

        if (time() > $data['expires_at']) {
            @unlink($path);
            return null;
        }

        return $data['payload'];
    }

    public function set(string $key, array $payload): void
    {
        if (!is_dir($this->cacheDir) && !mkdir($this->cacheDir, 0755, true)) {
            throw new RuntimeException('Unable to create cache directory');
        }

        $data = [
            'expires_at' => time() + $this->ttl,
            'payload' => $payload,
        ];

        $path = $this->pathFor($key);
        $written = file_put_contents($path, json_encode($data), LOCK_EX);

        if ($written === false) {
            throw new RuntimeException('Unable to write cache file');
        }
    }

    private function pathFor(string $key): string
    {
        return $this->cacheDir . '/' . hash('sha256', $key) . '.json';
    }
}
