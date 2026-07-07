<?php

class TelegramNotifier
{
    private const API_BASE = 'https://api.telegram.org/bot';

    public function __construct(
        private readonly string $botToken,
        private readonly string $chatId,
    ) {}

    public function sendProgressReport(array $spaces, string $updatedAt): void
    {
        $message = $this->buildProgressMessage($spaces, $updatedAt);
        $this->sendMessage($message);
    }

    public function buildProgressMessage(array $spaces, string $updatedAt): string
    {
        $lines = [
            '📊 گزارش پیشرفت پروژه‌ها',
            '🕐 ' . $this->formatUpdatedAt($updatedAt),
            '',
        ];

        if (empty($spaces)) {
            $lines[] = 'هیچ پروژه‌ای یافت نشد.';

            return implode("\n", $lines);
        }

        foreach ($spaces as $space) {
            $lines[] = $this->formatSpaceBlock($space);
            $lines[] = '';
        }

        return rtrim(implode("\n", $lines));
    }

    private function formatSpaceBlock(array $space): string
    {
        $name = $space['name'] ?? 'Unknown';
        $hasEstimate = (bool) ($space['has_estimate'] ?? false);
        $progress = (float) ($space['progress'] ?? 0);
        $percent = formatPercent($progress, $hasEstimate);
        $bar = emojiProgressBar($progress, $hasEstimate);
        $remainingDays = formatRemainingDays(
            (int) ($space['remaining_ms'] ?? 0),
            $hasEstimate,
        );

        $block = "📁 {$name}\n";
        $block .= "{$percent}  {$bar}\n";

        if ($remainingDays !== null) {
            $block .= "⏳ {$remainingDays}";
        } elseif ($hasEstimate && (int) ($space['remaining_ms'] ?? 0) <= 0) {
            $block .= '✅ تکمیل شده';
        } else {
            $block .= '⚠️ بدون برآورد زمانی';
        }

        return $block;
    }

    private function formatUpdatedAt(string $updatedAt): string
    {
        $timestamp = strtotime($updatedAt);
        if ($timestamp === false) {
            return $updatedAt;
        }

        return date('Y/m/d H:i', $timestamp);
    }

    private function sendMessage(string $text): void
    {
        $url = self::API_BASE . $this->botToken . '/sendMessage';
        $payload = json_encode([
            'chat_id' => $this->chatId,
            'text' => $text,
            'disable_web_page_preview' => true,
        ], JSON_UNESCAPED_UNICODE);

        $ch = curl_init($url);
        if ($ch === false) {
            throw new RuntimeException('Failed to initialize cURL');
        }

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new RuntimeException('Telegram API request failed: ' . $error);
        }

        $decoded = json_decode($response, true);
        if ($httpCode !== 200 || !($decoded['ok'] ?? false)) {
            $description = $decoded['description'] ?? $response;
            throw new RuntimeException('Telegram API error: ' . $description);
        }
    }
}
