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
            default:
        }

        TelegramBotHelper::sendMessage($chatId, $responseMessage);
    }

    private function checkAuth($chatId): bool
    {
        return $chatId == TelegramBotHelper::myId();
    }
}
