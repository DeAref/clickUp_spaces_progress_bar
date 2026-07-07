<div align="center">

# 📊 ClickUp Space Progress Dashboard
<img width="1920" height="934" alt="Screen Shot 1405-04-15 at 14 29 20" src="https://github.com/user-attachments/assets/17e6270f-d5ae-4af1-9b55-96581e592ffa" />

### One glance. Every Space. Real progress.

**A feature ClickUp doesn't have — and Premium won't unlock it either.**

[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![ClickUp](https://img.shields.io/badge/ClickUp-API%20v2-7B68EE?style=for-the-badge&logo=clickup&logoColor=white)](https://clickup.com/)
[![No Premium](https://img.shields.io/badge/Premium-Not%20Required-2ECC71?style=for-the-badge)]()
[![Embed Ready](https://img.shields.io/badge/Dashboard-Embed%20Ready-FF6B6B?style=for-the-badge)]()
[![Open Source](https://img.shields.io/badge/Open%20Source-Free%20Forever-3498DB?style=for-the-badge)]()

<br>

![Progress Preview](https://img.shields.io/badge/▓▓▓▓▓▓▓▓░░░░-67%25-7B68EE?style=flat-square)
![Progress Preview](https://img.shields.io/badge/▓▓▓▓▓▓▓▓▓▓▓▓-94%25-2ECC71?style=flat-square)
![Progress Preview](https://img.shields.io/badge/▓▓▓░░░░░░░░░-23%25-E74C3C?style=flat-square)

*Live progress bars for every Space — right inside your ClickUp Dashboard*

<br>

[✨ Why you need it](#-why-you-need-it) •
[🚀 Quick start](#-quick-start) •
[📺 Embed in ClickUp](#-embed-in-clickup) •
[📱 Telegram reports](#-telegram-reports) •
[⚙️ Technical docs](#️-technical-documentation)

</div>

---

## 😤 The problem

You run projects in ClickUp. Multiple Spaces. Multiple teams. Real work happening everywhere.

But when you need to know **how far along we actually are** — you're stuck.

| What you want | What ClickUp gives you |
|:---:|:---:|
| Real progress % per Space | ❌ |
| Estimate vs. Time Tracked comparison | ❌ |
| All Spaces on one screen | ❌ |
| Embed inside your Dashboard | ❌ |
| Access on Business / Enterprise | ❌ Still not a thing |

> You have **Time Estimates**. You have **Time Tracking**.  
> But you don't have a **Progress Bar**. Not on Free. Not on Premium. Not on any plan.

---

## ✨ Why you need it

### 🎯 One glance, full picture

No more jumping Space by Space, List by List, filtering manually, and adding up estimates in a spreadsheet.

One dashboard. Every Space. Real progress percentages.

### 📈 Real numbers, not gut feelings

```
Progress = Effective Time Spent ÷ Time Estimated × 100

> Done tasks with no time tracked count as fully spent (estimate).
```

Built on actual ClickUp data — not team vibes, not manager guesswork.

### 🖼️ Lives inside ClickUp

Add it as an **Embed Card** on your ClickUp Dashboard.  
Open your Dashboard and see where every project stands — without leaving ClickUp.

### 💸 Free. Actually free.

- No extra subscription
- No paid add-on
- No Premium gate
- Just a simple PHP host

---

## 🎬 What you'll see

```
┌─────────────────────────────────────────────┐
│  [🟣] Product Design              67.4%    │
│       ████████████░░░░░░░░                  │
│                                             │
│  [🔵] Engineering                 94.1%    │
│       ███████████████████░                  │
│                                             │
│  [🟢] Marketing                   23.0%    │
│       ████░░░░░░░░░░░░░░░░                  │
│                                             │
│  [🟡] Operations            No estimate    │
│       ░░░░░░░░░░░░░░░░░░░░                  │
└─────────────────────────────────────────────┘
```

- ✅ Space icon from ClickUp (or custom initials fallback)
- ✅ Each Space's own color on the progress bar
- ✅ Remaining time label per Space (days / hours)
- ✅ **Analytics page** — charts, KPIs, and comparison tables (`analytics.php`)
- ✅ **Telegram reports** — scheduled progress summaries to a group chat
- ✅ **Smart done-task handling** — completed tasks without time tracking count as fully spent
- ✅ Smooth animations
- ✅ Auto-refresh every 5 minutes
- ✅ Built for embed — minimal margins, pure white background

---

## 🚀 Quick start

### Requirements

| Need | Details |
|:---:|:---:|
| PHP | 8.0 or higher |
| Extensions | `curl`, `json` |
| ClickUp | Personal API Token |
| Hosting | Any PHP host with HTTPS |

### Install in 3 minutes

```bash
# 1. Clone
git clone https://github.com/your-username/clickup-space-progress.git
cd clickup-space-progress

# 2. Configure
cp config.example.php config.php
# Add your ClickUp token to config.php

# 3. Run
php -S localhost:8080
```

Open: **http://localhost:8080**

| Page | URL | Purpose |
|------|-----|---------|
| Progress dashboard | `index.php` | Embed card — progress bars per Space |
| Analytics | `analytics.php` | Charts, KPIs, estimate vs. spent comparison |
| JSON API | `api.php` | Data for auto-refresh (`?refresh=1` bypasses cache) |
| Telegram cron | `telegram_notify.php` | Send progress report to Telegram |

---

## 📱 Telegram reports

Send a formatted progress summary to a Telegram group on a schedule.

### 1. Create a bot

1. Open [@BotFather](https://t.me/BotFather) in Telegram
2. Send `/newbot` and follow the steps
3. Copy the **bot token** (e.g. `123456:ABC-DEF...`)

### 2. Add the bot to your group

1. Add the bot to the target group
2. Send a test message in the group
3. Open `https://api.telegram.org/bot<TOKEN>/getUpdates` in a browser
4. Find `"chat":{"id":-100...}` — that negative number is your **chat ID**

### 3. Configure `config.php`

```php
'telegram_bot_token'   => '123456:ABC-DEF...',
'telegram_chat_id'     => '-1001234567890',
'telegram_cron_secret' => 'a-long-random-secret-string',
```

### 4. Test manually

```bash
# CLI (no secret needed)
php telegram_notify.php

# HTTP (secret required)
curl "https://your-domain.com/telegram_notify.php?key=a-long-random-secret-string"
```

### 5. Schedule with cron

Run every morning at 9:00 (server time):

```cron
0 9 * * * curl -fsS "https://your-domain.com/telegram_notify.php?key=YOUR_SECRET" > /dev/null
```

Or via CLI on the server:

```cron
0 9 * * * /usr/bin/php /path/to/project/telegram_notify.php
```

**Sample Telegram message:**

```
📊 گزارش پیشرفت پروژه‌ها
🕐 2026/07/07 09:00

📁 Product Design
67.4%  🟩🟩🟩🟩🟩🟩🟩⬜⬜⬜
⏳ 12 روز مانده تا تکمیل
```

---

## 📺 Embed in ClickUp

1. Deploy the project to a host with **HTTPS**
2. Go to your **Dashboard** in ClickUp
3. **+ Add card** → **Embeds and Apps** → **Custom Embed**
4. Paste the URL to `index.php`
5. Resize the card — done!

> 💡 **Tip:** This dashboard is designed for ClickUp's rectangular card layout — tight margins, maximum data density.

---

## 🆚 Comparison

| Feature | ClickUp Native | This project |
|:---:|:---:|:---:|
| Time Estimate | ✅ | ✅ (via API) |
| Time Tracking | ✅ | ✅ (via API) |
| Progress bar per Space | ❌ | ✅ |
| Analytics dashboard | ❌ | ✅ |
| Telegram progress reports | ❌ | ✅ |
| Dashboard embed | ❌ | ✅ |
| All Spaces at once | ❌ | ✅ |
| Requires Premium | — | ❌ |
| Monthly cost | $7–$19+ | **$0** |

---

## 🧩 Perfect for

<table>
<tr>
<td width="50%">

**👔 Project managers**
> I open my Dashboard every morning and instantly see which Space is behind — no status meeting required.

</td>
<td width="50%">

**🎨 Creative teams**
> Three Folders under one Space? All of them roll up into a single progress bar.

</td>
</tr>
<tr>
<td>

**💼 Freelancers & agencies**
> Multiple clients, multiple Spaces. One glance is enough.

</td>
<td>

**🚀 Startups**
> No budget for extra tools, but a real need for visibility.

</td>
</tr>
</table>

---

## ❓ FAQ

<details>
<summary><b>Isn't this on Business or Enterprise?</b></summary>
<br>
No. ClickUp still doesn't offer Space-level progress bars based on Estimate vs. Tracked Time. This project fills that gap.
</details>

<details>
<summary><b>What if a Space has multiple Folders?</b></summary>
<br>
No problem. Tasks from all Folders and Lists under that Space are aggregated into one overall percentage.
</details>

<details>
<summary><b>How often does it update?</b></summary>
<br>
Server-side cache: 5 minutes. Browser auto-refresh: every 5 minutes. Both configurable in <code>config.php</code>.
</details>

<details>
<summary><b>Is it secure?</b></summary>
<br>
Your API token stays server-side only. It is never exposed in HTML or JavaScript.
</details>

<details>
<summary><b>What happens to completed tasks without time tracking?</b></summary>
<br>
If a task is <b>Done</b> (<code>status.type = closed</code>) and has a time estimate but no tracked time, the dashboard counts its full estimate as spent. This keeps progress accurate when the team closes tasks without logging time.
</details>

<details>
<summary><b>What if a task was done faster than the estimate?</b></summary>
<br>
If time was tracked, the <b>real tracked time</b> is used — progress for that task will be less than 100%. Only done tasks with <b>zero</b> tracked time get credited the full estimate.
</details>

<details>
<summary><b>How do I force a data refresh?</b></summary>
<br>
Append <code>?refresh=1</code> to <code>api.php</code> or reload the page after cache expires. Telegram cron always fetches fresh data.
</details>

---

## ⚙️ Technical documentation

### Project structure

```
project-progress-bar/
├── index.php                # Main progress dashboard (embed target)
├── analytics.php            # Analytics page — charts & KPIs
├── api.php                  # JSON endpoint for auto-refresh
├── telegram_notify.php      # Telegram cron endpoint / CLI script
├── bootstrap.php            # Config, cache, helpers, Telegram sender
├── config.example.php       # Sample configuration
├── config.php               # Real config (gitignored — create locally)
├── cache/                   # File cache (must be writable)
├── src/
│   ├── ClickUpClient.php    # ClickUp API v2 HTTP client (cURL)
│   ├── ProgressService.php  # Space progress calculation
│   ├── FileCache.php        # TTL-based file cache
│   └── TelegramNotifier.php # Telegram message builder & sender
└── assets/
    ├── style.css            # Dashboard styles
    ├── app.js               # Dashboard auto-refresh
    ├── analytics.css        # Analytics page styles
    └── analytics.js         # Analytics charts (client-side)
```

### Step-by-step setup

#### 1. Get a ClickUp API token

1. Log in to ClickUp
2. Go to **Settings → Apps → API Token**
3. Click **Generate** and copy the token (starts with `pk_`)

#### 2. Create configuration

```bash
cp config.example.php config.php
```

Edit `config.php`:

```php
<?php

return [
    // Required
    'clickup_token' => 'pk_YOUR_TOKEN_HERE',

    // Optional — null uses the first workspace in your account
    'team_id' => null,

    // Server-side cache TTL in seconds (default: 300 = 5 min)
    'cache_ttl' => 300,

    // Browser auto-refresh interval in seconds (default: 300 = 5 min)
    'refresh_interval' => 300,

    // Telegram (optional — only needed for telegram_notify.php)
    'telegram_bot_token'   => 'YOUR_BOT_TOKEN',
    'telegram_chat_id'     => '-1001234567890',
    'telegram_cron_secret' => 'change-me-to-a-long-random-string',
];
```

| Key | Required | Description |
|-----|----------|-------------|
| `clickup_token` | ✅ | ClickUp Personal API Token |
| `team_id` | ❌ | Workspace ID. Leave `null` to auto-select the first workspace |
| `cache_ttl` | ❌ | How long API results are cached on disk (seconds) |
| `refresh_interval` | ❌ | How often the browser polls `api.php` (seconds) |
| `telegram_bot_token` | ❌ | Bot token from @BotFather |
| `telegram_chat_id` | ❌ | Target chat/group ID (negative for groups) |
| `telegram_cron_secret` | ❌ | Secret key for HTTP cron (`?key=...`). Not needed for CLI |

#### 3. Prepare cache directory

```bash
mkdir -p cache
chmod 755 cache
```

The web server user must be able to write to `cache/`.

#### 4. Run locally

```bash
php -S localhost:8080
```

| URL | What you get |
|-----|--------------|
| http://localhost:8080/index.php | Progress bars |
| http://localhost:8080/analytics.php | Analytics dashboard |
| http://localhost:8080/api.php | Raw JSON |
| http://localhost:8080/api.php?refresh=1 | Fresh JSON (bypass cache) |

#### 5. Deploy to production

1. Upload all files **except** `config.php` (create it on the server)
2. Ensure PHP **8.0+** with `curl` and `json` extensions
3. Set `cache/` permissions: `chmod 755 cache`
4. Verify HTTPS is enabled (required for ClickUp embed cards)
5. Test: open `https://your-domain.com/index.php`
6. Embed `index.php` URL in ClickUp Dashboard

**Apache** — if you use `.htaccess`, make sure `cache/` is not publicly listable.

**Nginx** — point the vhost document root to the project folder; no special rewrite rules needed.

#### 6. Set up Telegram cron (optional)

See [📱 Telegram reports](#-telegram-reports) above.

### ClickUp API endpoints used

| Endpoint | Purpose |
|----------|---------|
| `GET /api/v2/team` | Resolve workspace (when `team_id` is null) |
| `GET /api/v2/team/{id}/space` | List all Spaces |
| `GET /api/v2/team/{id}/task` | All tasks (paginated, includes closed + subtasks) |

Query parameters on the tasks endpoint:

- `include_closed=true` — includes completed tasks
- `subtasks=true` — includes subtasks in rollup
- `page=N` — pagination (100 tasks per page)

### Progress formula

```
progress% = Σ(effective_spent) / Σ(time_estimate) × 100
```

#### Which tasks count?

| Condition | Included? |
|-----------|-----------|
| Task has `time_estimate > 0` | ✅ Yes — added to denominator |
| Task has no estimate | ❌ Skipped entirely |
| Space is archived | ❌ Excluded |
| Subtasks | ✅ Included in Space rollup |

#### Effective spent per task

| Task state | `time_spent` | `effective_spent` |
|------------|-------------|-------------------|
| Open / in progress | 0 | 0 |
| Open / in progress | tracked | actual `time_spent` |
| Done (`status.type = closed`) | 0 | full `time_estimate` |
| Done (`status.type = closed`) | tracked | actual `time_spent` |

> **Why?** Teams often close tasks without logging time. Crediting the estimate on done tasks prevents progress from staying artificially low.

#### Display rules

- Progress bar width is capped at **100%** (even if spent > estimate)
- Percentage label shows the **real number** (can exceed 100% → "over budget")
- Spaces with no estimated tasks show **"بدون برآورد"** (no estimate)
- Remaining time = `max(0, total_estimate − total_spent)`

### JSON API response (`api.php`)

```json
{
  "ok": true,
  "updated_at": "2026-07-07T12:00:00+00:00",
  "spaces": [
    {
      "space_id": "123",
      "name": "Product Design",
      "color": "#7B68EE",
      "progress": 67.4,
      "total_estimate_ms": 144000000,
      "total_spent_ms": 97113600,
      "remaining_ms": 46886400,
      "task_count": 42,
      "estimated_task_count": 38,
      "has_estimate": true,
      "estimate_label": "40 روز",
      "spent_label": "27 روز",
      "remaining_label": "13 روز مانده",
      "status": "on_track"
    }
  ],
  "summary": {
    "overall_progress": 55.2,
    "on_track": 3,
    "over_budget": 1,
    "no_estimate": 1
  }
}
```

### Security notes

- `config.php` is gitignored — never commit API tokens
- `telegram_cron_secret` protects the HTTP cron endpoint (403 without valid `?key=`)
- ClickUp token is only used server-side; never sent to the browser
- Use HTTPS in production

### Troubleshooting

| Problem | Fix |
|---------|-----|
| `config.php not found` | Copy `config.example.php` → `config.php` |
| `Set your ClickUp API token` | Replace placeholder token in `config.php` |
| Empty Spaces list | Check token permissions; verify `team_id` |
| Stale data | Use `api.php?refresh=1` or wait for cache TTL |
| Telegram 403 | Check `telegram_cron_secret` matches `?key=` param |
| Telegram "chat not found" | Bot must be added to the group; use correct negative chat ID |
| Slow first load | Normal for large workspaces — cache speeds up subsequent loads |
| Progress seems low | Ensure tasks have time estimates; done tasks without tracking are now auto-credited |

### Known limitations

- Only tasks your API token can access are included
- Large workspaces → slower first load (cache helps after that)
- ClickUp rate limit: ~100 requests/min
- Task completion is detected via `status.type = closed` (ClickUp's closed/done statuses)
- Progress is time-based, not task-count-based

---

<div align="center">

<br>

**Built for people who track time but can't see progress.**

[![Star this repo](https://img.shields.io/badge/⭐-Star%20this%20repo-yellow?style=for-the-badge)]()
[![Share with team](https://img.shields.io/badge/📤-Share%20with%20team-7B68EE?style=for-the-badge)]()

<br>

*If this README helped, drop a ⭐ — maybe one day ClickUp adds it too. Until then, we've got you.* 😉

</div>
