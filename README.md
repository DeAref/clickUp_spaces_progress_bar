<div align="center">

# 📊 ClickUp Space Progress Dashboard

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
Progress = Time Spent ÷ Time Estimated × 100
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

---

<br>

---

## ⚙️ Technical documentation

### Project structure

```
project-progress-bar/
├── index.php              # Main HTML page
├── api.php                # JSON endpoint for auto-refresh
├── bootstrap.php          # Config, cache, helpers
├── config.example.php     # Sample configuration
├── config.php             # Real config (gitignored)
├── cache/                 # Cache files
├── src/
│   ├── ClickUpClient.php  # cURL HTTP client
│   ├── ProgressService.php
│   └── FileCache.php
└── assets/
    ├── style.css
    └── app.js
```

### Configuration (`config.php`)

```php
return [
    'clickup_token' => 'pk_...',   // Personal API Token
    'team_id'       => null,       // null = first workspace
    'cache_ttl'     => 300,        // seconds — server-side cache
    'refresh_interval' => 300,     // seconds — browser auto-refresh
];
```

Get your token from: **ClickUp → Settings → Apps → API Token**

### ClickUp API endpoints used

| Endpoint | Purpose |
|----------|---------|
| `GET /api/v2/team` | Resolve workspace |
| `GET /api/v2/team/{id}/space` | List Spaces |
| `GET /api/v2/team/{id}/task` | All tasks (paginated) |

### Progress formula

```
progress% = Σ(time_spent) / Σ(time_estimate) × 100
```

- Only tasks with `time_estimate > 0` count toward the denominator
- `time_spent` and `time_estimate` are in milliseconds
- If spent > estimate → show the real number, cap the bar at 100%
- Archived Spaces are excluded

### Deploy on shared hosting

1. Upload the files
2. Create `config.php`
3. Make the `cache/` folder writable (`chmod 755`)
4. Verify PHP 8+ and the `curl` extension
5. Embed the URL in your ClickUp Dashboard

### Known limitations

- Only tasks your API token can access are included
- Large workspaces → slower first load (cache helps after that)
- ClickUp rate limit: ~100 requests/min

---

<div align="center">

<br>

**Built for people who track time but can't see progress.**

[![Star this repo](https://img.shields.io/badge/⭐-Star%20this%20repo-yellow?style=for-the-badge)]()
[![Share with team](https://img.shields.io/badge/📤-Share%20with%20team-7B68EE?style=for-the-badge)]()

<br>

*If this README helped, drop a ⭐ — maybe one day ClickUp adds it too. Until then, we've got you.* 😉

</div>
