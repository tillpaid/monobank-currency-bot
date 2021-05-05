<?php

return [
    'botUserName'   => env('BOT_USER_NAME', ''),
    'botApiToken'   => env('BOT_API_TOKEN', ''),
    'botWebhookUrl' => env('APP_URL', '') . '/telegram-webhook',
    'myChatId'      => env('TELEGRAM_MY_CHAT_ID', ''),
];
