<?php

namespace App\Telegram\Processes\ProcessState\Sell;

use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;
use Illuminate\Database\Eloquent\Model;

class ProcessTelegramSellSumState extends AbstractProcessTelegramState
{
    public function process(Model $user, string $messageText): string
    {
        switch (true) {
            case $messageText == __('telegram_buttons.back'):
                $this->updateUserState($user, config('states.sell'));
                $responseMessage = __('telegram.chooseCurrency');

                break;
            case $messageText == (string)(float)$messageText:
                $currencySumAll = $user->state_additional['sell-currency-sum-all'] ?? 0;

                if ($messageText > $currencySumAll) {
                    $responseMessage = __('telegram.moreThanHave');
                } else {
                    $responseMessage = $messageText . ' | Processed';
                }
                
                break;
            default:
                $responseMessage = __('telegram.occurredError');
        }

        return $responseMessage;
    }
}
