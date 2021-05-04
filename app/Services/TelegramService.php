<?php

namespace App\Services;

use App\Helpers\TelegramBotHelper;
use App\Services\Interfaces\TelegramServiceInterface;

class TelegramService implements TelegramServiceInterface
{
    public function processWebhook(array $data): void
    {
        $message = array_key_exists('edited_message', $data)
            ? $data['edited_message']
            : $data['message'];

        $chatId = $message['chat']['id'];
        $messageText = $message['text'];

        $this->processMessage($chatId, $messageText);
    }

    public function sendMessageAboutChangeEnv(): void
    {
        $chatId = TelegramBotHelper::myId();
        $message = __('telegram.environmentChanged', ['env' => config('app.env')]);

        TelegramBotHelper::sendMessage($chatId, $message);
    }

    private function processMessage($chatId, $messageText): void
    {
        if (!$this->checkAuth($chatId)) {
            TelegramBotHelper::sendMessage($chatId, __('telegram.notAuth'));
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

        TelegramBotHelper::sendMessage($chatId, $responseMessage);
    }

    private function checkAuth($chatId): bool
    {
        return $chatId == TelegramBotHelper::myId();
    }
}
