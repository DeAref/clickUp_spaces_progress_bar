<?php

/**
 * Send project progress to Telegram.
 *
 * HTTP (external cron):
 *   GET https://your-domain.com/telegram_notify.php?key=YOUR_SECRET
 *
 * CLI:
 *   php telegram_notify.php
 */

require_once __DIR__ . '/bootstrap.php';

$isCli = PHP_SAPI === 'cli';

if (!$isCli) {
    header('Content-Type: application/json; charset=utf-8');

    $config = loadAppConfig();
    $secret = trim((string) ($config['telegram_cron_secret'] ?? ''));
    $providedKey = (string) ($_GET['key'] ?? '');

    if ($secret === '' || !hash_equals($secret, $providedKey)) {
        http_response_code(403);
        echo json_encode([
            'ok' => false,
            'error' => 'Forbidden',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

try {
    $result = sendTelegramProgressReport();

    if ($isCli) {
        fwrite(STDOUT, '[' . date('c') . "] Progress report sent ({$result['space_count']} projects).\n");
        exit(0);
    }

    echo json_encode([
        'ok' => true,
        'updated_at' => $result['updated_at'],
        'space_count' => $result['space_count'],
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    if ($isCli) {
        fwrite(STDERR, '[' . date('c') . '] ' . $e->getMessage() . "\n");
        exit(1);
    }

    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
