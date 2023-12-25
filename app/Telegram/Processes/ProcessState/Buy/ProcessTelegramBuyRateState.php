<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState\Buy;

use App\Models\TelegramUser;
use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;
use Illuminate\Database\Eloquent\Model;

class ProcessTelegramBuyRateState extends AbstractProcessTelegramState
{
    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        switch ($messageText) {
            case __('telegram_buttons.back'):
                $this->updateUserState($telegramUser, config('states.buy-sum'));
                $responseMessage = __('telegram.buySum');

                break;

            case $messageText === __('telegram_buttons.backHome'):
                $this->updateUserState($telegramUser, null);
                $responseMessage = __('telegram.startMessage');

                break;

            case __('telegram_buttons.confirm'):
                $this->updateUserState($telegramUser, null);
                $this->currencyAccountService->create(
                    $telegramUser->id,
                    $telegramUser->state_additional['buy-currency'],
                    (float) $telegramUser->state_additional['buy-currency-sum'],
                    (float) $telegramUser->state_additional['buy-currency-rate']
                );

                $responseMessage = __('telegram.buySuccessMessage');
                $responseMessage .= __('telegram.delimiter');
                $responseMessage .= $this->telegramBotService->buildUserBalanceMessage($telegramUser->id);

                break;

            case __('telegram_buttons.editRate'):
                $this->updateUserState($telegramUser, config('states.buy-rate-own'));
                $responseMessage = __('telegram.changeRateMessage');

                break;

            default:
                $responseMessage = __('telegram.occurredError');
        }

        return $responseMessage;
    }
}
