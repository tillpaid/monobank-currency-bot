<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState\Sell;

use App\Models\TelegramUser;
use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;

class ProcessTelegramSellConfirmState extends AbstractProcessTelegramState
{
    public function getState(): ?string
    {
        return TelegramUser::STATE_SELL_CONFIRM;
    }

    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        switch ($messageText) {
            case __('telegram_buttons.back'):
                // TODO: A default value is don't needed here. Update the code to remove it. And the same for the other states in all places.
                $currency = $telegramUser->getStateAdditionalValue(TelegramUser::STATE_ADDITIONAL_SELL_CURRENCY) ?? 'usd';
                $currencySum = $telegramUser->getStateAdditionalFloatValue(TelegramUser::STATE_ADDITIONAL_SELL_CURRENCY_SUM_ALL) ?? 0;
                $currencySum = number_format($currencySum, 5, '.', ' ');

                $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_SELL_SUM);
                $responseMessage = __('telegram.sellSum', ['currencySum' => $currencySum, 'currency' => mb_strtoupper($currency)]);

                break;

            case $messageText === __('telegram_buttons.backHome'):
                $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_DEFAULT);
                $responseMessage = __('telegram.startMessage');

                break;

            case __('telegram_buttons.confirm'):
                $currency = $telegramUser->getStateAdditionalValue(TelegramUser::STATE_ADDITIONAL_SELL_CURRENCY) ?? 'usd';
                $currencySum = $telegramUser->getStateAdditionalFloatValue(TelegramUser::STATE_ADDITIONAL_SELL_CURRENCY_SUM) ?? 0;

                $this->currencyAccountService->sellCurrency($telegramUser->getId(), $currency, $currencySum);
                $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_DEFAULT);

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
