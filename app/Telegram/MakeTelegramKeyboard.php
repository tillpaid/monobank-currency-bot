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
                $keyboard = [[__('telegram_buttons.back')]];
                break;
            case config('states.buy-rate'):
                $keyboard = [[__('telegram_buttons.confirm'), __('telegram_buttons.editRate')], [__('telegram_buttons.back')]];
                break;
        }

        return $keyboard;
    }
}
