<?php

declare(strict_types=1);

namespace App\Telegram;

use App\Models\TelegramUser;

class MakeTelegramKeyboard
{
    /**
     * @return string[][]
     */
    public function getKeyboard(?string $state): array
    {
        return match ($state) {
            null => $this->getMainMenuKeyboard(),
            TelegramUser::STATE_BUY, TelegramUser::STATE_SELL, TelegramUser::STATE_STATISTICS_CURRENCY => $this->getCurrenciesAndBackKeyboard(),
            TelegramUser::STATE_BUY_SUM, TelegramUser::STATE_BUY_RATE_OWN, TelegramUser::STATE_SELL_SUM => $this->getBackKeyboard(),
            TelegramUser::STATE_BUY_RATE => $this->getEditRateKeyboard(),
            TelegramUser::STATE_SELL_CONFIRM => $this->getConfirmKeyboard(),
            default => [],
        };
    }

    private function getMainMenuKeyboard(): array
    {
        return [
            [__('telegram_buttons.buy'), __('telegram_buttons.sell')],
            [__('telegram_buttons.balance'), __('telegram_buttons.report')],
            [__('telegram_buttons.statisticsCurrency')],
        ];
    }

    private function getCurrenciesAndBackKeyboard(): array
    {
        return [
            array_map('mb_strtoupper', config('monobank.currencies')),
            [__('telegram_buttons.back')],
        ];
    }

    private function getBackKeyboard(): array
    {
        return [
            [__('telegram_buttons.back'), __('telegram_buttons.backHome')],
        ];
    }

    private function getEditRateKeyboard(): array
    {
        return [
            [__('telegram_buttons.editRate')],
            [__('telegram_buttons.confirm')],
            [__('telegram_buttons.back')],
        ];
    }

    private function getConfirmKeyboard(): array
    {
        return [
            [__('telegram_buttons.confirm')],
            [__('telegram_buttons.back')],
        ];
    }
}
