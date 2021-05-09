<?php

namespace App\Telegram;

class MakeTelegramKeyboard
{
    public function getKeyboard(?string $state): array
    {
        switch ($state) {
            case config('states.buy'):
                $keyboard = [['Any key..']];
                break;
            default:
                $keyboard = [[__('telegram_buttons.buy')]];
        }

        return $keyboard;
    }
}
