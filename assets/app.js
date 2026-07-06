(function () {
    const dashboard = document.getElementById('dashboard');
    if (!dashboard) {
        return;
    }

    const intervalSeconds = parseInt(dashboard.dataset.refreshInterval || '300', 10);
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

    function progressBarWidth(progress, hasEstimate) {
        if (!hasEstimate) {
            return 0;
        }
        return Math.min(100, Math.max(0, Number(progress)));
    }

    function renderSpaces(spaces) {
        if (!spaces.length) {
            dashboard.innerHTML = '<div class="empty-state">هیچ Space فعالی یافت نشد.</div>';
            return;
        }

        const rows = spaces.map(function (space) {
            const barWidth = progressBarWidth(space.progress, space.has_estimate);
            const percentLabel = formatPercent(space.progress, space.has_estimate);
            const color = escapeHtml(space.color || '#7B68EE');
            const avatar = space.avatar
                ? '<img class="space-avatar" src="' + escapeHtml(space.avatar) + '" alt="" width="32" height="32" loading="lazy">'
                : '<div class="space-avatar avatar-fallback" style="background-color: ' + color + '">' + escapeHtml(space.initials) + '</div>';

            return (
                '<article class="space-row" data-space-id="' + escapeHtml(space.space_id) + '">' +
                    '<div class="space-header">' +
                        '<div class="space-identity">' +
                            avatar +
                            '<span class="space-name">' + escapeHtml(space.name) + '</span>' +
                        '</div>' +
                        '<span class="space-percent' + (space.has_estimate ? '' : ' no-estimate') + '">' +
                            escapeHtml(percentLabel) +
                        '</span>' +
                    '</div>' +
                    '<div class="progress-track' + (space.has_estimate ? '' : ' no-estimate') + '">' +
                        '<div class="progress-fill" style="width: ' + barWidth + '%; --space-color: ' + color + '"></div>' +
                    '</div>' +
                '</article>'
            );
        }).join('');

        dashboard.innerHTML = '<div class="space-list">' + rows + '</div>';
    }

    async function refresh() {
        try {
            const response = await fetch('api.php');
            const data = await response.json();

            if (!data.ok) {
                dashboard.innerHTML = '<div class="error-state">' + escapeHtml(data.error || 'خطا در بارگذاری داده') + '</div>';
                return;
            }

            renderSpaces(data.spaces || []);
        } catch (error) {
            dashboard.innerHTML = '<div class="error-state">خطا در ارتباط با سرور</div>';
        }
    }

    setInterval(refresh, intervalMs);
})();
