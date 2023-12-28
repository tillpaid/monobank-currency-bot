<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState\Sell;

use App\Models\TelegramUser;
use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;

class ProcessTelegramSellConfirmState extends AbstractProcessTelegramState
{
    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        switch ($messageText) {
            case __('telegram_buttons.back'):
                $currency = $telegramUser->getStateAdditional()['sell-currency'] ?? 'usd';
                $currencySum = $telegramUser->getStateAdditional()['sell-currency-sum-all'] ?? 0;
                $currencySum = number_format($currencySum, 5, '.', ' ');

                $this->updateUserState($telegramUser, config('states.sell-sum'));
                $responseMessage = __('telegram.sellSum', ['currencySum' => $currencySum, 'currency' => mb_strtoupper($currency)]);

                break;

            case $messageText === __('telegram_buttons.backHome'):
                $this->updateUserState($telegramUser, null);
                $responseMessage = __('telegram.startMessage');

                break;

            case __('telegram_buttons.confirm'):
                $currency = $telegramUser->getStateAdditional()['sell-currency'] ?? 'usd';
                $currencySum = $telegramUser->getStateAdditional()['sell-currency-sum'] ?? 0;

                $this->currencyAccountService->sellCurrency($telegramUser->getId(), $currency, $currencySum);
                $this->updateUserState($telegramUser, null);

                $responseMessage = __('telegram.sellSuccessMessage');
                $responseMessage .= __('telegram.delimiter');
                $responseMessage .= $this->telegramBotService->buildUserBalanceMessage($telegramUser->getId());

                break;

            default:
                $responseMessage = __('telegram.occurredError');
        }

        return $responseMessage;
    }
}
