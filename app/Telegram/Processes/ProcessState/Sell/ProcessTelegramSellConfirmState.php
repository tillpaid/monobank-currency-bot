<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState\Sell;

use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;
use Illuminate\Database\Eloquent\Model;

class ProcessTelegramSellConfirmState extends AbstractProcessTelegramState
{
    public function process(Model $user, string $messageText): string
    {
        switch ($messageText) {
            case __('telegram_buttons.back'):
                $currency = $user->state_additional['sell-currency'] ?? 'usd';
                $currencySum = $user->state_additional['sell-currency-sum-all'] ?? 0;
                $currencySum = number_format($currencySum, 5, '.', ' ');

                $this->updateUserState($user, config('states.sell-sum'));
                $responseMessage = __('telegram.sellSum', ['currencySum' => $currencySum, 'currency' => mb_strtoupper($currency)]);

                break;

            case $messageText === __('telegram_buttons.backHome'):
                $this->updateUserState($user, null);
                $responseMessage = __('telegram.startMessage');

                break;

            case __('telegram_buttons.confirm'):
                $currency = $user->state_additional['sell-currency'] ?? 'usd';
                $currencySum = $user->state_additional['sell-currency-sum'] ?? 0;

                $this->currencyAccountService->sellCurrency($user->id, $currency, $currencySum);
                $this->updateUserState($user, null);

                $responseMessage = __('telegram.sellSuccessMessage');
                $responseMessage .= __('telegram.delimiter');
                $responseMessage .= $this->telegramBotService->buildUserBalanceMessage($user->id);

                break;

            default:
                $responseMessage = __('telegram.occurredError');
        }

        return $responseMessage;
    }
}
