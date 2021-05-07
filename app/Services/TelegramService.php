<?php

namespace App\Services;

use App\Models\TelegramUser;
use App\Services\Interfaces\Telegram\TelegramBotServiceInterface;
use App\Services\Interfaces\Telegram\TelegramServiceInterface;


class TelegramService implements TelegramServiceInterface
{
    private $telegramBotService;

    public function __construct(TelegramBotServiceInterface $telegramBotService)
    {
        $this->telegramBotService = $telegramBotService;
    }

    public function processWebhook(array $data): void
    {
        $message = array_key_exists('edited_message', $data)
            ? $data['edited_message']
            : $data['message'];

        $chatId = $message['chat']['id'];
        $messageText = $message['text'];

        $this->createUserIfNotExists($chatId);
        $this->processMessage($chatId, $messageText);
    }

    public function sendMessageAboutChangeEnv(): void
    {
        $chatId = $this->telegramBotService->getMyId();
        $message = __('telegram.environmentChanged', ['env' => config('app.env')]);

        $this->telegramBotService->sendMessage($chatId, $message);
    }

    private function processMessage($chatId, $messageText): void
    {
        if (!$this->checkAuth($chatId)) {
            $this->telegramBotService->sendMessage($chatId, __('telegram.notAuth'));
            return;
        }

        $responseMessage = $messageText;

        switch ($messageText) {
            case '/start':
                $responseMessage = __('telegram.startMessage');
                break;
            case '/env':
                $responseMessage = __('telegram.environment', ['env' => config('app.env')]);
                break;
            default:
        }

        $this->telegramBotService->sendMessage($chatId, $responseMessage);
    }

    private function checkAuth($chatId): bool
    {
        return $chatId == $this->telegramBotService->getMyId();
    }

    private function createUserIfNotExists($chatId): void
    {
        if (!TelegramUser::where('chat_id', $chatId)->count()) {
            TelegramUser::create(['chat_id' => $chatId]);
        }
    }
}
