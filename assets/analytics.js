(function () {
    const analytics = document.getElementById('analytics');
    if (!analytics) {
        return;
    }

    const intervalSeconds = parseInt(analytics.dataset.refreshInterval || '300', 10);
    const intervalMs = Math.max(60, intervalSeconds) * 1000;

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function formatPercent(progress, hasEstimate) {
        if (!hasEstimate) {
            return 'بدون برآورد';
        }
        return Number(progress).toFixed(1) + '%';
    }

    function formatDuration(ms) {
        if (!ms || ms <= 0) {
            return '۰';
        }

        const totalMinutes = Math.round(ms / 60000);

        if (totalMinutes < 60) {
            return totalMinutes + ' دقیقه';
        }

        const hours = Math.floor(totalMinutes / 60);
        const minutes = totalMinutes % 60;

        if (hours < 24) {
            if (minutes === 0) {
                return hours + ' ساعت';
            }
            return hours + ' ساعت و ' + minutes + ' دقیقه';
        }

        const days = Math.floor(hours / 24);
        const remHours = hours % 24;

        if (remHours === 0) {
            return days + ' روز';
        }

        return days + ' روز و ' + remHours + ' ساعت';
    }

    function formatRemainingTime(remainingMs, hasEstimate) {
        if (!hasEstimate || remainingMs <= 0) {
            return '';
        }

        const totalMinutes = Math.ceil(remainingMs / 60000);

        if (totalMinutes < 60) {
            return totalMinutes + ' دقیقه مانده';
        }

        const hours = Math.floor(totalMinutes / 60);
        const minutes = totalMinutes % 60;

        if (hours < 24) {
            if (minutes === 0) {
                return hours + ' ساعت مانده';
            }
            return hours + ' ساعت و ' + minutes + ' دقیقه مانده';
        }

        const days = Math.floor(hours / 24);
        const remHours = hours % 24;

        if (remHours === 0) {
            return days + ' روز مانده';
        }

        return days + ' روز و ' + remHours + ' ساعت مانده';
    }

    function computeSummary(spaces) {
        let totalEstimate = 0;
        let totalSpent = 0;
        let withEstimate = 0;
        let onTrack = 0;
        let overBudget = 0;
        let noEstimate = 0;

        spaces.forEach(function (space) {
            if (space.has_estimate) {
                withEstimate++;
                totalEstimate += space.total_estimate_ms || 0;
                totalSpent += space.total_spent_ms || 0;

                if (space.status === 'over_budget') {
                    overBudget++;
                } else {
                    onTrack++;
                }
            } else {
                noEstimate++;
            }
        });

        const overallProgress = totalEstimate > 0
            ? Math.round((totalSpent / totalEstimate) * 1000) / 10
            : 0;

        return {
            with_estimate: withEstimate,
            on_track: onTrack,
            over_budget: overBudget,
            no_estimate: noEstimate,
            total_estimate_ms: totalEstimate,
            total_spent_ms: totalSpent,
            remaining_ms: Math.max(0, totalEstimate - totalSpent),
            overall_progress: overallProgress,
            total_estimate_label: formatDuration(totalEstimate),
            total_spent_label: formatDuration(totalSpent),
            remaining_label: formatRemainingTime(Math.max(0, totalEstimate - totalSpent), totalEstimate > 0),
        };
    }

    function renderKpis(summary) {
        const progressEl = analytics.querySelector('[data-kpi="overall-progress"]');
        const remainingEl = analytics.querySelector('[data-kpi="remaining"]');
        const estimateEl = analytics.querySelector('[data-kpi="total-estimate"]');
        const spentEl = analytics.querySelector('[data-kpi="total-spent"]');
        const statusEl = analytics.querySelector('[data-kpi="status-breakdown"]');

        if (progressEl) {
            progressEl.textContent = summary.total_estimate_ms > 0
                ? summary.overall_progress.toFixed(1) + '%'
                : '—';
        }
        if (remainingEl) {
            remainingEl.textContent = summary.remaining_label || '';
        }
        if (estimateEl) {
            estimateEl.textContent = summary.total_estimate_label;
        }
        if (spentEl) {
            spentEl.textContent = summary.total_spent_label;
        }
        if (statusEl) {
            statusEl.innerHTML =
                '<span class="status-dot on-track"></span>' + summary.on_track +
                '<span class="status-dot over-budget"></span>' + summary.over_budget +
                '<span class="status-dot no-estimate"></span>' + summary.no_estimate;
        }
    }

    function renderProgressChart(spaces) {
        const container = document.getElementById('chart-progress');
        if (!container) {
            return;
        }

        const withEstimate = spaces
            .filter(function (s) { return s.has_estimate; })
            .sort(function (a, b) { return b.progress - a.progress; });

        if (!withEstimate.length) {
            container.innerHTML = '<div class="empty-state">داده‌ای برای نمایش نیست</div>';
            return;
        }

        const rows = withEstimate.map(function (space) {
            const width = Math.min(100, Math.max(0, space.progress));
            const color = escapeHtml(space.color || '#7B68EE');
            const overClass = space.progress > 100 ? ' over-budget' : '';

            return (
                '<div class="bar-row">' +
                    '<span class="bar-label" title="' + escapeHtml(space.name) + '">' + escapeHtml(space.name) + '</span>' +
                    '<div class="bar-track">' +
                        '<div class="bar-fill' + overClass + '" style="width:' + width + '%;background:linear-gradient(90deg,' + color + ',color-mix(in srgb,' + color + ' 75%,#fff))"></div>' +
                    '</div>' +
                    '<span class="bar-value">' + escapeHtml(formatPercent(space.progress, true)) + '</span>' +
                '</div>'
            );
        }).join('');

        container.innerHTML = '<div class="bar-chart">' + rows + '</div>';
    }

    function renderComparisonChart(spaces) {
        const container = document.getElementById('chart-comparison');
        if (!container) {
            return;
        }

        const withEstimate = spaces
            .filter(function (s) { return s.has_estimate; })
            .sort(function (a, b) { return (b.total_estimate_ms || 0) - (a.total_estimate_ms || 0); });

        if (!withEstimate.length) {
            container.innerHTML = '<div class="empty-state">داده‌ای برای نمایش نیست</div>';
            return;
        }

        const maxMs = Math.max.apply(null, withEstimate.map(function (s) {
            return Math.max(s.total_estimate_ms || 0, s.total_spent_ms || 0);
        }));

        const rows = withEstimate.map(function (space) {
            const color = escapeHtml(space.color || '#7B68EE');
            const estWidth = maxMs > 0 ? ((space.total_estimate_ms || 0) / maxMs) * 100 : 0;
            const spentWidth = maxMs > 0 ? ((space.total_spent_ms || 0) / maxMs) * 100 : 0;

            return (
                '<div class="comparison-row">' +
                    '<span class="bar-label" title="' + escapeHtml(space.name) + '">' + escapeHtml(space.name) + '</span>' +
                    '<div class="comparison-bars">' +
                        '<div class="comparison-bar-wrap">' +
                            '<span class="comparison-bar-label">برآورد</span>' +
                            '<div class="comparison-bar-track">' +
                                '<div class="comparison-bar-fill estimate" style="width:' + estWidth + '%"></div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="comparison-bar-wrap">' +
                            '<span class="comparison-bar-label">ثبت‌شده</span>' +
                            '<div class="comparison-bar-track">' +
                                '<div class="comparison-bar-fill spent" style="width:' + spentWidth + '%;--space-color:' + color + '"></div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
        }).join('');

        container.innerHTML = '<div class="comparison-chart">' + rows + '</div>';
    }

    function renderDistributionChart(spaces) {
        const container = document.getElementById('chart-distribution');
        if (!container) {
            return;
        }

        const withEstimate = spaces.filter(function (s) { return s.has_estimate; });
        const total = withEstimate.reduce(function (sum, s) {
            return sum + (s.total_estimate_ms || 0);
        }, 0);

        if (!withEstimate.length || total <= 0) {
            container.innerHTML = '<div class="empty-state">داده‌ای برای نمایش نیست</div>';
            return;
        }

        const segments = withEstimate.map(function (space) {
            return {
                space: space,
                pct: ((space.total_estimate_ms || 0) / total) * 100,
            };
        });

        const circumference = 2 * Math.PI * 40;
        let cumulative = 0;

        const circles = segments.map(function (seg) {
            const color = escapeHtml(seg.space.color || '#7B68EE');
            const length = (seg.pct / 100) * circumference;
            const gap = circumference - length;
            const offset = -cumulative;
            cumulative += length;

            return (
                '<circle cx="50" cy="50" r="40" fill="transparent" stroke="' + color + '" stroke-width="16" ' +
                'stroke-dasharray="' + length + ' ' + gap + '" stroke-dashoffset="' + offset + '" ' +
                'transform="rotate(-90 50 50)"></circle>'
            );
        }).join('');

        const legend = segments
            .sort(function (a, b) { return b.pct - a.pct; })
            .map(function (seg) {
                const color = escapeHtml(seg.space.color || '#7B68EE');
                return (
                    '<div class="legend-item">' +
                        '<span class="legend-dot" style="background:' + color + '"></span>' +
                        '<span class="legend-name">' + escapeHtml(seg.space.name) + '</span>' +
                        '<span class="legend-pct">' + seg.pct.toFixed(1) + '%</span>' +
                    '</div>'
                );
            }).join('');

        container.innerHTML =
            '<svg class="donut-chart" viewBox="0 0 100 100" aria-hidden="true">' +
                '<circle cx="50" cy="50" r="40" fill="#f0f0f0"></circle>' +
                circles +
            '</svg>' +
            '<div class="donut-legend">' + legend + '</div>';
    }

    function renderStatusChart(spaces, summary) {
        const container = document.getElementById('chart-status');
        if (!container) {
            return;
        }

        const total = spaces.length;
        if (!total) {
            container.innerHTML = '<div class="empty-state">داده‌ای برای نمایش نیست</div>';
            return;
        }

        const onPct = (summary.on_track / total) * 100;
        const overPct = (summary.over_budget / total) * 100;
        const noPct = (summary.no_estimate / total) * 100;

        container.innerHTML =
            '<div class="status-chart">' +
                '<div class="status-bar">' +
                    '<div class="status-bar-track">' +
                        (summary.on_track ? '<div class="status-segment on-track" style="width:' + onPct + '%"></div>' : '') +
                        (summary.over_budget ? '<div class="status-segment over-budget" style="width:' + overPct + '%"></div>' : '') +
                        (summary.no_estimate ? '<div class="status-segment no-estimate" style="width:' + noPct + '%"></div>' : '') +
                    '</div>' +
                '</div>' +
                '<div class="status-legend">' +
                    '<div class="status-legend-item">' +
                        '<span class="status-legend-left"><span class="status-dot on-track"></span>در مسیر</span>' +
                        '<span>' + summary.on_track + ' Space</span>' +
                    '</div>' +
                    '<div class="status-legend-item">' +
                        '<span class="status-legend-left"><span class="status-dot over-budget"></span>بیش‌ازحد</span>' +
                        '<span>' + summary.over_budget + ' Space</span>' +
                    '</div>' +
                    '<div class="status-legend-item">' +
                        '<span class="status-legend-left"><span class="status-dot no-estimate"></span>بدون برآورد</span>' +
                        '<span>' + summary.no_estimate + ' Space</span>' +
                    '</div>' +
                '</div>' +
            '</div>';
    }

    function renderTable(spaces) {
        const container = document.getElementById('chart-table');
        if (!container) {
            return;
        }

        const rows = spaces.map(function (space) {
            const color = escapeHtml(space.color || '#7B68EE');
            const estimateLabel = space.estimate_label || formatDuration(space.total_estimate_ms || 0);
            const spentLabel = space.spent_label || formatDuration(space.total_spent_ms || 0);
            const remainingLabel = space.remaining_label || '—';

            return (
                '<tr>' +
                    '<td><span class="table-space"><span class="table-dot" style="background:' + color + '"></span>' + escapeHtml(space.name) + '</span></td>' +
                    '<td>' + escapeHtml(formatPercent(space.progress, space.has_estimate)) + '</td>' +
                    '<td>' + escapeHtml(estimateLabel) + '</td>' +
                    '<td>' + escapeHtml(spentLabel) + '</td>' +
                    '<td>' + escapeHtml(remainingLabel) + '</td>' +
                    '<td>' + (space.estimated_task_count || 0) + ' / ' + (space.task_count || 0) + '</td>' +
                '</tr>'
            );
        }).join('');

        container.innerHTML =
            '<table class="summary-table">' +
                '<thead><tr>' +
                    '<th>Space</th><th>پیشرفت</th><th>برآورد</th><th>ثبت‌شده</th><th>مانده</th><th>تسک‌ها</th>' +
                '</tr></thead>' +
                '<tbody>' + rows + '</tbody>' +
            '</table>';
    }

    function renderAll(spaces, summary) {
        renderKpis(summary);
        renderProgressChart(spaces);
        renderComparisonChart(spaces);
        renderDistributionChart(spaces);
        renderStatusChart(spaces, summary);
        renderTable(spaces);
    }

    function loadInitialData() {
        const el = document.getElementById('analytics-data');
        if (!el) {
            return null;
        }

        try {
            return JSON.parse(el.textContent);
        } catch (error) {
            return null;
        }
    }

    async function refresh() {
        try {
            const response = await fetch('api.php');
            const data = await response.json();

            if (!data.ok) {
                analytics.innerHTML = '<div class="error-state">' + escapeHtml(data.error || 'خطا در بارگذاری داده') + '</div>';
                return;
            }

            const spaces = data.spaces || [];
            if (!spaces.length) {
                analytics.innerHTML = '<div class="empty-state">هیچ Space فعالی یافت نشد.</div>';
                return;
            }

            const summary = data.summary || computeSummary(spaces);
            renderAll(spaces, summary);
        } catch (error) {
            analytics.innerHTML = '<div class="error-state">خطا در ارتباط با سرور</div>';
        }
    }

    const initial = loadInitialData();
    if (initial && initial.spaces && initial.spaces.length) {
        renderAll(initial.spaces, initial.summary || computeSummary(initial.spaces));
    } else {
        refresh();
    }

    setInterval(refresh, intervalMs);
})();
