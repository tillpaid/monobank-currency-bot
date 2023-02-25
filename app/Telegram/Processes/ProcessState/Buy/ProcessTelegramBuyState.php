<?php

namespace App\Telegram\Processes\ProcessState\Buy;

use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;
use Illuminate\Database\Eloquent\Model;

class ProcessTelegramBuyState extends AbstractProcessTelegramState
{
    public function process(Model $user, string $messageText): string
    {
        $messageTextLower = mb_strtolower($messageText);

        switch (true) {
            case in_array($messageTextLower, config('monobank.currencies')):
                $this->updateUserState($user, config('states.buy-sum'), ['buy-currency' => $messageTextLower]);
                $responseMessage = __('telegram.buySum');

                break;
            case $messageText == __('telegram_buttons.back'):
                $this->updateUserState($user, null);
                $responseMessage = __('telegram.startMessage');

                break;
            default:
                $responseMessage = __('telegram.currencyNotSupported');
        }

        return $responseMessage;
    }
}
