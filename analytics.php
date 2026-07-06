<?php

require_once __DIR__ . '/bootstrap.php';

$error = null;
$data = ['spaces' => [], 'updated_at' => null];
$summary = [];
$config = [];

try {
    $config = loadAppConfig();
    $data = getProgressData();
    $summary = computeAnalyticsSummary($data['spaces']);
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
    <title>Space Analytics</title>
    <link rel="stylesheet" href="assets/analytics.css">
</head>
<body>
    <main
        id="analytics"
        class="analytics"
        data-refresh-interval="<?= escape((string) $refreshInterval) ?>"
    >
        <?php if ($error !== null): ?>
            <div class="error-state"><?= escape($error) ?></div>
        <?php elseif (empty($data['spaces'])): ?>
            <div class="empty-state">هیچ Space فعالی یافت نشد.</div>
        <?php else: ?>
            <section class="kpi-row">
                <article class="kpi-card">
                    <span class="kpi-label">پیشرفت کلی</span>
                    <span class="kpi-value" data-kpi="overall-progress">
                        <?= $summary['total_estimate_ms'] > 0 ? escape(number_format($summary['overall_progress'], 1) . '%') : '—' ?>
                    </span>
                    <span class="kpi-sub" data-kpi="remaining"><?= escape($summary['remaining_label'] ?? '') ?></span>
                </article>
                <article class="kpi-card">
                    <span class="kpi-label">زمان برآورد شده</span>
                    <span class="kpi-value" data-kpi="total-estimate"><?= escape($summary['total_estimate_label']) ?></span>
                    <span class="kpi-sub"><?= (int) $summary['with_estimate'] ?> Space</span>
                </article>
                <article class="kpi-card">
                    <span class="kpi-label">زمان ثبت‌شده</span>
                    <span class="kpi-value" data-kpi="total-spent"><?= escape($summary['total_spent_label']) ?></span>
                    <span class="kpi-sub">Time Tracked</span>
                </article>
                <article class="kpi-card">
                    <span class="kpi-label">وضعیت Spaceها</span>
                    <span class="kpi-value kpi-value-sm" data-kpi="status-breakdown">
                        <span class="status-dot on-track"></span><?= (int) $summary['on_track'] ?>
                        <span class="status-dot over-budget"></span><?= (int) $summary['over_budget'] ?>
                        <span class="status-dot no-estimate"></span><?= (int) $summary['no_estimate'] ?>
                    </span>
                    <span class="kpi-sub">در مسیر / بیش‌ازحد / بدون برآورد</span>
                </article>
            </section>

            <div class="charts-grid">
                <section class="chart-card chart-wide" aria-label="مقایسه پیشرفت Spaceها">
                    <h2 class="chart-title">پیشرفت هر Space</h2>
                    <div class="chart-body" id="chart-progress"></div>
                </section>

                <section class="chart-card" aria-label="سهم زمان برآورد">
                    <h2 class="chart-title">سهم برآورد زمانی</h2>
                    <div class="chart-body chart-donut-wrap" id="chart-distribution"></div>
                </section>

                <section class="chart-card chart-wide" aria-label="برآورد در مقابل ثبت‌شده">
                    <h2 class="chart-title">برآورد vs ثبت‌شده</h2>
                    <div class="chart-body" id="chart-comparison"></div>
                </section>

                <section class="chart-card" aria-label="وضعیت بودجه">
                    <h2 class="chart-title">وضعیت بودجه</h2>
                    <div class="chart-body" id="chart-status"></div>
                </section>

                <section class="chart-card chart-full" aria-label="جزئیات Spaceها">
                    <h2 class="chart-title">جدول خلاصه</h2>
                    <div class="chart-body table-wrap" id="chart-table">
                        <table class="summary-table">
                            <thead>
                                <tr>
                                    <th>Space</th>
                                    <th>پیشرفت</th>
                                    <th>برآورد</th>
                                    <th>ثبت‌شده</th>
                                    <th>مانده</th>
                                    <th>تسک‌ها</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['spaces'] as $space): ?>
                                    <tr>
                                        <td>
                                            <span class="table-space">
                                                <span class="table-dot" style="background: <?= escape($space['color']) ?>"></span>
                                                <?= escape($space['name']) ?>
                                            </span>
                                        </td>
                                        <td><?= escape(formatPercent($space['progress'], $space['has_estimate'])) ?></td>
                                        <td><?= escape($space['estimate_label']) ?></td>
                                        <td><?= escape($space['spent_label']) ?></td>
                                        <td><?= escape($space['remaining_label'] ?? '—') ?></td>
                                        <td><?= (int) $space['estimated_task_count'] ?> / <?= (int) $space['task_count'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        <?php endif; ?>
    </main>
    <?php if ($error === null && !empty($data['spaces'])): ?>
        <script type="application/json" id="analytics-data"><?= json_encode(
            ['spaces' => $data['spaces'], 'summary' => $summary],
            JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT,
        ) ?></script>
    <?php endif; ?>
    <script src="assets/analytics.js"></script>
</body>
</html>
