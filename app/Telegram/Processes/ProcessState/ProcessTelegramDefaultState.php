<?php

namespace App\Telegram\Processes\ProcessState;

use Illuminate\Database\Eloquent\Model;

class ProcessTelegramDefaultState extends AbstractProcessTelegramState
{
    public function process(Model $user, string $messageText): string
    {
        switch ($messageText) {
            case __('telegram_buttons.buy'):
                $this->updateUserState($user, config('states.buy'));
                $responseMessage = 'Press any key..';

                break;
            default:
                $responseMessage = __('telegram.occurredError');
        }

        return $responseMessage;
    }
}
