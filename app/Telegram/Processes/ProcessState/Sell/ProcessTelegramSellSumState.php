<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState\Sell;

use App\Models\TelegramUser;
use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;

class ProcessTelegramSellSumState extends AbstractProcessTelegramState
{
    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        switch (true) {
            case $messageText === __('telegram_buttons.back'):
                $this->updateUserState($telegramUser, config('states.sell'));
                $responseMessage = __('telegram.chooseCurrencySell');

                break;

            case $messageText === __('telegram_buttons.backHome'):
                $this->updateUserState($telegramUser, null);
                $responseMessage = __('telegram.startMessage');

                break;

            case is_numeric($messageText):
                $messageText = (float) $messageText;

                $currency = $telegramUser->state_additional['sell-currency'] ?? 'usd';
                $currencySumAll = $telegramUser->state_additional['sell-currency-sum-all'] ?? 0;

                if ($messageText > 0) {
                    if ($currencySumAll >= $messageText) {
                        $currencySum = number_format($messageText, 5, '.', ' ');

                        $this->updateUserState($telegramUser, config('states.sell-confirm'), ['sell-currency-sum' => $messageText]);
                        $responseMessage = __('telegram.sellConfirm', ['sum' => $currencySum, 'currency' => mb_strtoupper($currency)]);
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
