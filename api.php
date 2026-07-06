<?php

require_once __DIR__ . '/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $forceRefresh = isset($_GET['refresh']) && $_GET['refresh'] === '1';
    $data = getProgressData($forceRefresh);

    echo json_encode([
        'ok' => true,
        'updated_at' => $data['updated_at'],
        'spaces' => $data['spaces'],
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
