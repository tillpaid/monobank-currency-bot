<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState\Sell;

use App\Models\TelegramUser;
use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;

class ProcessTelegramSellState extends AbstractProcessTelegramState
{
    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        $messageTextLower = mb_strtolower($messageText);

        switch (true) {
            case in_array($messageTextLower, config('monobank.currencies'), true):
                $currencySum = $this->currencyAccountService->getUserCurrencySum($telegramUser->id, $messageTextLower);

                if ($currencySum > 0) {
                    $this->updateUserState($telegramUser, config('states.sell-sum'), ['sell-currency' => $messageTextLower, 'sell-currency-sum-all' => $currencySum]);

                    $currencySum = number_format($currencySum, 5, '.', ' ');
                    $responseMessage = __('telegram.sellSum', ['currencySum' => $currencySum, 'currency' => mb_strtoupper($messageText)]);
                } else {
                    $responseMessage = __('telegram.sellEmptySum');
                }

                break;

            case $messageText === __('telegram_buttons.back'):
                $this->updateUserState($telegramUser, null);
                $responseMessage = __('telegram.startMessage');

                break;

            default:
                $responseMessage = __('telegram.currencyNotSupported');
        }

        return $responseMessage;
    }
}
