<?php

namespace App\Telegram\Processes\ProcessState\Buy;

use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProcessTelegramBuySumState
 * @package App\Telegram\Processes\ProcessState\Buy
 */
class ProcessTelegramBuySumState extends AbstractProcessTelegramState
{
    /**
     * @param Model $user
     * @param string $messageText
     * @return string
     */
    public function process(Model $user, string $messageText): string
    {
        switch (true) {
            case $messageText == __('telegram_buttons.back'):
                $this->updateUserState($user, config('states.buy'));
                $responseMessage = __('telegram.chooseCurrencyBuy');

                break;
            case $messageText == (string)(float)$messageText:
                if ($messageText > 0) {
                    $this->updateUserState($user, config('states.buy-rate'), ['buy-currency-sum' => $messageText]);
                    $responseMessage = $this->buildBuyConfirmMessage($user);
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
