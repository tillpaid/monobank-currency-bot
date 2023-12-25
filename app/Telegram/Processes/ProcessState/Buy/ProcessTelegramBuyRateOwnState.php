<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState\Buy;

use App\Models\TelegramUser;
use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;

class ProcessTelegramBuyRateOwnState extends AbstractProcessTelegramState
{
    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        switch (true) {
            case $messageText === __('telegram_buttons.back'):
                $this->updateUserState($telegramUser, config('states.buy-rate'));
                $responseMessage = $this->buildBuyConfirmMessage($telegramUser);

                break;

            case $messageText === __('telegram_buttons.backHome'):
                $this->updateUserState($telegramUser, null);
                $responseMessage = __('telegram.startMessage');

                break;

            case is_numeric($messageText):
                $messageText = (float) $messageText;

                if ($messageText > 0) {
                    $this->updateUserState($telegramUser, config('states.buy-rate'));
                    $responseMessage = $this->buildBuyConfirmMessage($telegramUser, (float) $messageText);
                } else {
                    $responseMessage = __('telegram.numberMustBeGreaterThanZero');
                }

                break;

            default:
                $responseMessage = __('telegram.occurredError');
        }

        return $responseMessage;
    }
}
