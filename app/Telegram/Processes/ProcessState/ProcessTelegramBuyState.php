<?php

namespace App\Telegram\Processes\ProcessState;

use Illuminate\Database\Eloquent\Model;

class ProcessTelegramBuyState extends AbstractProcessTelegramState
{
    public function process(Model $user, string $messageText): string
    {
        switch ($messageText) {
            default:
                $this->updateUserState($user, null);
                $responseMessage = 'Success';
        }

        return $responseMessage;
    }
}
