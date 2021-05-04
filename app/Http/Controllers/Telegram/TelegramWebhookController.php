<?php

namespace App\Http\Controllers\Telegram;

use App\Helpers\TelegramBotHelper;
use App\Http\Controllers\Controller;

class TelegramWebhookController extends Controller
{
    public function catchWebhook()
    {
        $telegram = TelegramBotHelper::getBot();
        $telegram->useGetUpdatesWithoutDatabase();

        $serverResponse = $telegram->handle();

        if ($serverResponse) {
            $this->processWebhook();
        }
    }

    private function processWebhook()
    {
        $message = request()->has('edited_message')
            ? request('edited_message')
            : request('message');

        $chatId = $message['chat']['id'];
        $messageText = $message['text'];

        TelegramBotHelper::sendMessage($chatId, $messageText);
    }
}
