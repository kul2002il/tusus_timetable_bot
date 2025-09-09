<?php

return [
    'api_key'       => env('TELEGRAM_BOT_API_TOKEN', ''),
    'admin_chat_id' => env('TELEGRAM_LOG_CHAT_ID'),

    'bot_username' => env('TELEGRAM_BOT_USERNAME', 'tusus_timetable_bot')
];
