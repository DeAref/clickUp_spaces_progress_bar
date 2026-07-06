<?php

require_once __DIR__ . '/bootstrap.php';

$error = null;
$data = ['spaces' => [], 'updated_at' => null];
$config = [];

try {
    $config = loadAppConfig();
    $data = getProgressData();
} catch (Throwable $e) {
    $error = $e->getMessage();
}

$refreshInterval = (int) ($config['refresh_interval'] ?? 300);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Space Progress</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <main id="dashboard" class="dashboard" data-refresh-interval="<?= escape((string) $refreshInterval) ?>">
        <?php if ($error !== null): ?>
            <div class="error-state"><?= escape($error) ?></div>
        <?php elseif (empty($data['spaces'])): ?>
            <div class="empty-state">هیچ Space فعالی یافت نشد.</div>
        <?php else: ?>
            <div class="space-list">
                <?php foreach ($data['spaces'] as $space): ?>
                    <?php
                    $barWidth = progressBarWidth($space['progress'], $space['has_estimate']);
                    $percentLabel = formatPercent($space['progress'], $space['has_estimate']);
                    $color = escape($space['color']);
                    ?>
                    <article class="space-row" data-space-id="<?= escape($space['space_id']) ?>">
                        <div class="space-header">
                            <div class="space-identity">
                                <?php if (!empty($space['avatar'])): ?>
                                    <img
                                        class="space-avatar"
                                        src="<?= escape($space['avatar']) ?>"
                                        alt=""
                                        width="32"
                                        height="32"
                                        loading="lazy"
                                    >
                                <?php else: ?>
                                    <div class="space-avatar avatar-fallback" style="background-color: <?= $color ?>">
                                        <?= escape($space['initials']) ?>
                                    </div>
                                <?php endif; ?>
                                <span class="space-name"><?= escape($space['name']) ?></span>
                            </div>
                            <span class="space-percent<?= $space['has_estimate'] ? '' : ' no-estimate' ?>">
                                <?= escape($percentLabel) ?>
                            </span>
                        </div>
                        <div class="progress-track<?= $space['has_estimate'] ? '' : ' no-estimate' ?>">
                            <div
                                class="progress-fill"
                                style="width: <?= $barWidth ?>%; --space-color: <?= $color ?>"
                            ></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    <script src="assets/app.js"></script>
</body>
</html>
