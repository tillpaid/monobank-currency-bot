<?php

namespace App\Telegram\Processes;

class ProcessTelegramCommand
{
    public function process(string $messageText): string
    {
        switch ($messageText) {
            case '/start':
                $responseMessage = __('telegram.startMessage');
                break;
            case '/env':
                $responseMessage = __('telegram.environment', ['env' => config('app.env')]);
                break;
            default:
                $responseMessage = __('telegram.commandNotFound');
        }

        return $responseMessage;
    }
}
