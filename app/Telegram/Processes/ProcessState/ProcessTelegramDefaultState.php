<?php

namespace App\Telegram\Processes\ProcessState;

use App\Telegram\Processes\ProcessState\Interfaces\ProcessTelegramStateInterface;
use Illuminate\Database\Eloquent\Model;

class ProcessTelegramDefaultState implements ProcessTelegramStateInterface
{
    public function process(Model $user, string $messageText): string
    {
        switch ($messageText) {
            default:
                $responseMessage = __('telegram.occurredError');
        }

        return $responseMessage;
    }
}
