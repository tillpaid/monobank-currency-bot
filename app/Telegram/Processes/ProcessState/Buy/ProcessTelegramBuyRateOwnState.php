<?php

namespace App\Telegram\Processes\ProcessState\Buy;

use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;
use Illuminate\Database\Eloquent\Model;

class ProcessTelegramBuyRateOwnState extends AbstractProcessTelegramState
{
    public function process(Model $user, string $messageText): string
    {
        switch (true) {
            case $messageText == __('telegram_buttons.back'):
                $this->updateUserState($user, config('states.buy-rate'));
                $responseMessage = $this->buildBuyConfirmMessage($user);

                break;
            case $messageText == (string)(float)$messageText:
                if ($messageText > 0) {
                    $this->updateUserState($user, config('states.buy-rate'));
                    $responseMessage = $this->buildBuyConfirmMessage($user, $messageText);
                } else {
                    $responseMessage = __('telegram.numberMustBeGreaterThanZero');
                }

                break;
            default:
                $responseMessage = __('telegram.occurredError');
        }

        return $responseMessage;
    }
}
