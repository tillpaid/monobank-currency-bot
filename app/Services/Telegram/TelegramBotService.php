<?php

namespace App\Services\Telegram;

use App\Services\Interfaces\Telegram\TelegramBotServiceInterface;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class TelegramBotService implements TelegramBotServiceInterface
{
    private $bot;

    public function sendMessage(string $chatId, string $message, array $keyboard = []): void
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
        $telegram = $this->getBot();
        Request::sendMessage($sendData);
    }

    public function getBot(): Telegram
    {
        if (is_null($this->bot)) {
            $botUserName = config('telegram.botUserName');
            $botApiKey = config('telegram.botApiToken');

            $this->bot = new Telegram($botApiKey, $botUserName);
        }

        return $this->bot;
    }

    public function getMyId(): string
    {
        return config('telegram.myChatId');
    }
}
