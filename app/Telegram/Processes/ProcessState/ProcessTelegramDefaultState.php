<?php

namespace App\Telegram\Processes\ProcessState;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProcessTelegramDefaultState
 * @package App\Telegram\Processes\ProcessState
 */
class ProcessTelegramDefaultState extends AbstractProcessTelegramState
{
    /**
     * @param Model $user
     * @param string $messageText
     * @return string
     */
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
            case __('telegram_buttons.report'):
                $responseMessage = $this->telegramBotService->buildUserReport($user->id);

                break;
            case __('telegram_buttons.statisticsCurrency'):
                $this->updateUserState($user, config('states.statistics-currency'));
                $responseMessage = __('telegram.chooseCurrency');

                break;
            default:
                $responseMessage = __('telegram.occurredError');
        }

        return $responseMessage;
    }
}
