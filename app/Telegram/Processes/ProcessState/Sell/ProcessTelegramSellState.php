<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState\Sell;

use App\Models\TelegramUser;
use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;

class ProcessTelegramSellState extends AbstractProcessTelegramState
{
    public function getState(): ?string
    {
        return TelegramUser::STATE_SELL;
    }

    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        if ($this->isMessageTextCurrency($messageText)) {
            return $this->processCurrency($telegramUser, $messageText);
        }

        return match ($messageText) {
            __('telegram_buttons.back') => $this->processBackButton($telegramUser),
            default => __('telegram.currencyNotSupported'),
        };
    }

    private function processCurrency(TelegramUser $telegramUser, string $messageText): string
    {
        $currency = mb_strtolower($messageText);
        $currencySum = $this->currencyAccountRepository->getUserCurrencySum($telegramUser->getId(), $currency);

        if ($currencySum <= 0) {
            return __('telegram.sellEmptySum');
        }

        $stateAdditional = [
            TelegramUser::STATE_ADDITIONAL_SELL_CURRENCY => $currency,
            TelegramUser::STATE_ADDITIONAL_SELL_CURRENCY_SUM_ALL => $currencySum,
        ];
        $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_SELL_SUM, $stateAdditional);

        $formattedSum = $this->telegramUserAdditionalStateResolver->getCurrencySumAllFormatted($telegramUser);

        return __('telegram.sellSum', ['currencySum' => $formattedSum, 'currency' => mb_strtoupper($currency)]);
    }

    private function processBackButton(TelegramUser $telegramUser): string
    {
        $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_DEFAULT);

        return __('telegram.startMessage');
    }

    private function isMessageTextCurrency(string $messageText): bool
    {
        $lowerMessageText = mb_strtolower($messageText);
        $currenciesList = config('monobank.currencies');

        return in_array($lowerMessageText, $currenciesList, true);
    }
}
