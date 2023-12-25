<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState\Buy;

use App\Models\TelegramUser;
use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;
use Illuminate\Database\Eloquent\Model;

class ProcessTelegramBuySumState extends AbstractProcessTelegramState
{
    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        switch (true) {
            case $messageText === __('telegram_buttons.back'):
                $this->updateUserState($telegramUser, config('states.buy'));
                $responseMessage = __('telegram.chooseCurrencyBuy');

                break;

            case $messageText === __('telegram_buttons.backHome'):
                $this->updateUserState($telegramUser, null);
                $responseMessage = __('telegram.startMessage');

                break;

            case is_numeric($messageText):
                $messageText = (float) $messageText;

                if ($messageText > 0) {
                    $this->updateUserState($telegramUser, config('states.buy-rate'), ['buy-currency-sum' => $messageText]);
                    $responseMessage = $this->buildBuyConfirmMessage($telegramUser);
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
