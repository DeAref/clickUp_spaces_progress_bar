<?php

return [
    'clickup_token' => 'pk_YOUR_TOKEN_HERE',
    'team_id' => null,
    'cache_ttl' => 300,
    'refresh_interval' => 300,

    // Telegram (telegram_notify.php)
    'telegram_bot_token' => 'YOUR_BOT_TOKEN',
    'telegram_chat_id' => '-1001234567890', // group chat id (negative number)
    'telegram_cron_secret' => 'change-me-to-a-long-random-string', // ?key=... in cron URL
];
