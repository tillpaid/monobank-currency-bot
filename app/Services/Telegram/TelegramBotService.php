<?php

namespace App\Services\Telegram;

use App\Services\Interfaces\Models\TelegramUserServiceInterface;
use App\Services\Interfaces\Telegram\TelegramBotServiceInterface;
use App\Telegram\MakeTelegramKeyboard;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class TelegramBotService implements TelegramBotServiceInterface
{
    private $bot;
    private $telegramUserService;
    private $makeTelegramKeyboard;

    public function __construct(
        TelegramUserServiceInterface $telegramUserService,
        MakeTelegramKeyboard $makeTelegramKeyboard
    )
    {
        $this->telegramUserService = $telegramUserService;
        $this->makeTelegramKeyboard = $makeTelegramKeyboard;
    }

    public function sendMessage(string $chatId, string $message): void
    {
        $user = $this->telegramUserService->getByChatId($chatId);
        $keyboard = $this->makeTelegramKeyboard->getKeyboard($user->state ?? null);

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
