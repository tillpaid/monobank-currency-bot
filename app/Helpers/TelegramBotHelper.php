<?php

namespace App\Helpers;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class TelegramBotHelper
{
    public static function sendMessage($chatId, $message, $keyboard = [])
    {
        $sendData = [
            'chat_id'      => $chatId,
            'text'         => $message,
            'reply_markup' => [
                'remove_keyboard' => true,
                'keyboard'        => $keyboard
            ]
        ];

        // Need for Request sendMessage code
        $telegram = self::getBot();
        Request::sendMessage($sendData);
    }

    public static function getBot(): Telegram
    {
        $botUserName = config('telegram.botUserName');
        $botApiKey = config('telegram.botApiToken');

        $telegram = new Telegram($botApiKey, $botUserName);

        return $telegram;
    }

    public static function myId(): string
    {
        return config('telegram.myChatId');
    }
}
