<?php

namespace App\Telegram;

class MakeTelegramKeyboard
{
    public function getKeyboard(?string $state): array
    {
        $keyboard = [];

        switch ($state) {
            case null:
                $keyboard = [[__('telegram_buttons.buy')]];
                break;
            case config('states.buy'):
                $keyboard = [config('monobank.currencies'), [__('telegram_buttons.back')]];
                break;
            case config('states.buy-sum'):
            case config('states.buy-rate-own'):
                $keyboard = [[__('telegram_buttons.back')]];
                break;
            case config('states.buy-rate'):
                $keyboard = [[__('telegram_buttons.back'), __('telegram_buttons.editRate')], [__('telegram_buttons.confirm')]];
                break;
        }

        return $keyboard;
    }
}
