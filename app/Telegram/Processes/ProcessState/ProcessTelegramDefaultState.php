<?php

namespace App\Telegram\Processes\ProcessState;

use Illuminate\Database\Eloquent\Model;

class ProcessTelegramDefaultState extends AbstractProcessTelegramState
{
    public function process(Model $user, string $messageText): string
    {
        switch ($messageText) {
            case __('telegram_buttons.buy'):
                $this->updateUserState($user, config('states.buy'));
                $responseMessage = __('telegram.chooseCurrencyBuy');

                break;
            case __('telegram_buttons.sell'):
                $this->updateUserState($user, config('states.sell'));
                $responseMessage = __('telegram.chooseCurrencySell');

                break;
            case __('telegram_buttons.balance'):
                $responseMessage = $this->telegramBotService->buildUserBalanceMessage($user->id);

                break;
            default:
                $responseMessage = __('telegram.occurredError');
        }

        return $responseMessage;
    }
}
