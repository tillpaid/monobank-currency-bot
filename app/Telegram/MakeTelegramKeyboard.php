<?php

namespace App\Telegram;

class MakeTelegramKeyboard
{
    public function getKeyboard(?string $state): array
    {
        $keyboard = [];

        switch ($state) {
            case null:
                $keyboard = [[__('telegram_buttons.buy'), __('telegram_buttons.sell')]];
                break;
            case config('states.buy'):
            case config('states.sell'):
                $keyboard = [config('monobank.currencies'), [__('telegram_buttons.back')]];
                break;
            case config('states.buy-sum'):
            case config('states.buy-rate-own'):
            case config('states.sell-sum'):
                $keyboard = [[__('telegram_buttons.back')]];
                break;
            case config('states.buy-rate'):
                $keyboard = [[__('telegram_buttons.back'), __('telegram_buttons.editRate')], [__('telegram_buttons.confirm')]];
                break;
            case config('states.sell-confirm'):
                $keyboard = [[__('telegram_buttons.confirm')], [__('telegram_buttons.back')]];
                break;
        }

        return $keyboard;
    }
}
