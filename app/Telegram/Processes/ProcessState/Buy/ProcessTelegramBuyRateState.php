<?php

namespace App\Telegram\Processes\ProcessState\Buy;

use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProcessTelegramBuyRateState
 * @package App\Telegram\Processes\ProcessState\Buy
 */
class ProcessTelegramBuyRateState extends AbstractProcessTelegramState
{
    /**
     * @param Model $user
     * @param string $messageText
     * @return string
     */
    public function process(Model $user, string $messageText): string
	{
        switch ($messageText) {
            case __('telegram_buttons.back'):
                $this->updateUserState($user, config('states.buy-sum'));
                $responseMessage = __('telegram.buySum');

                break;
            case $messageText == __('telegram_buttons.backHome'):
                $this->updateUserState($user, null);
                $responseMessage = __('telegram.startMessage');

                break;
            case __('telegram_buttons.confirm'):
                $this->updateUserState($user, null);
                $this->currencyAccountService->create(
                    $user->id,
                    $user->state_additional['buy-currency'],
                    $user->state_additional['buy-currency-sum'],
                    $user->state_additional['buy-currency-rate']
                );

                $responseMessage = __('telegram.buySuccessMessage');
                $responseMessage .= __('telegram.delimiter');
                $responseMessage .= $this->telegramBotService->buildUserBalanceMessage($user->id);

                break;
            case __('telegram_buttons.editRate'):
                $this->updateUserState($user, config('states.buy-rate-own'));
                $responseMessage = __('telegram.changeRateMessage');

                break;
            default:
                $responseMessage = __('telegram.occurredError');
        }

        return $responseMessage;
	}
}
