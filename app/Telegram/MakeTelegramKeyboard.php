<?php

declare(strict_types=1);

namespace App\Telegram;

class MakeTelegramKeyboard
{
    /**
     * @return string[][]
     */
    public function getKeyboard(?string $state): array
    {
        return match ($state) {
            null => $this->getMainMenuKeyboard(),
            config('states.buy'), config('states.sell'), config('states.statistics-currency') => $this->getCurrenciesAndBackKeyboard(),
            config('states.buy-sum'), config('states.buy-rate-own'), config('states.sell-sum') => $this->getBackKeyboard(),
            config('states.buy-rate') => $this->getEditRateKeyboard(),
            config('states.sell-confirm') => $this->getConfirmKeyboard(),
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
