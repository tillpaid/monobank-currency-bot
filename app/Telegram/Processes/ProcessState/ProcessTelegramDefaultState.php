<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState;

use App\Models\TelegramUser;

class ProcessTelegramDefaultState extends AbstractProcessTelegramState
{
    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        switch ($messageText) {
            case __('telegram_buttons.buy'):
                $this->updateUserState($telegramUser, config('states.buy'));
                $responseMessage = __('telegram.chooseCurrencyBuy');

                break;

            case __('telegram_buttons.sell'):
                $this->updateUserState($telegramUser, config('states.sell'));
                $responseMessage = __('telegram.chooseCurrencySell');

                break;

            case __('telegram_buttons.balance'):
                $responseMessage = $this->telegramBotService->buildUserBalanceMessage($telegramUser->id);

                break;

            case __('telegram_buttons.report'):
                $responseMessage = $this->telegramBotService->buildUserReport($telegramUser->id);

                break;

            case __('telegram_buttons.statisticsCurrency'):
                $this->updateUserState($telegramUser, config('states.statistics-currency'));
                $responseMessage = __('telegram.chooseCurrency');

                break;

            default:
                $responseMessage = __('telegram.occurredError');
        }

        return $responseMessage;
    }
}
