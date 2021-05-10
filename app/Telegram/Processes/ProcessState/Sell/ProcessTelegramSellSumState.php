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
                $responseMessage = __('telegram.chooseCurrencySell');

                break;
            case $messageText == (string)(float)$messageText:
                $currency = $user->state_additional['sell-currency'] ?? 'usd';
                $currencySumAll = $user->state_additional['sell-currency-sum-all'] ?? 0;

                if ($messageText > 0) {
                    if ($currencySumAll >= $messageText) {
                        $currencySum = number_format($messageText, 5, '.', ' ');

                        $this->updateUserState($user, config('states.sell-confirm'), ['sell-currency-sum' => $messageText]);
                        $responseMessage = __('telegram.sellConfirm', ['sum' => $currencySum, 'currency' => $currency]);
                    } else {
                        $responseMessage = __('telegram.moreThanHave');
                    }
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
