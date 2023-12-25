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
        $keyboard = [];

        switch ($state) {
            case null:
                $keyboard = [
                    [__('telegram_buttons.buy'), __('telegram_buttons.sell')],
                    [__('telegram_buttons.balance'), __('telegram_buttons.report')],
                    [__('telegram_buttons.statisticsCurrency')],
                ];

                break;

            case config('states.buy'):
            case config('states.sell'):
            case config('states.statistics-currency'):
                $keyboard = [
                    $this->getCurrencies(),
                    [__('telegram_buttons.back')],
                ];

                break;

            case config('states.buy-sum'):
            case config('states.buy-rate-own'):
            case config('states.sell-sum'):
                $keyboard = [
                    [__('telegram_buttons.back'), __('telegram_buttons.backHome')],
                ];

                break;

            case config('states.buy-rate'):
                $keyboard = [
                    [__('telegram_buttons.editRate')],
                    [__('telegram_buttons.confirm')],
                    [__('telegram_buttons.back'), __('telegram_buttons.backHome')],
                ];

                break;

            case config('states.sell-confirm'):
                $keyboard = [
                    [__('telegram_buttons.confirm')],
                    [__('telegram_buttons.back'), __('telegram_buttons.backHome')],
                ];

                break;
        }

        return $keyboard;
    }

    /**
     * @return string[]
     */
    private function getCurrencies(): array
    {
        return array_map('mb_strtoupper', config('monobank.currencies'));
    }
}
