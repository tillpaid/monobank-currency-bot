<?php

namespace App\Telegram;

class MakeTelegramKeyboard
{
    public function getKeyboard(?string $state): array
    {
        switch ($state) {
            default:
                $keyboard = [[__('telegram_buttons.buy')]];
        }

        return $keyboard;
    }
}
