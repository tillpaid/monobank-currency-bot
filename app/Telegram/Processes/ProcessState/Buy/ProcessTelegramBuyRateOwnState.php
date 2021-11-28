<?php

namespace App\Telegram\Processes\ProcessState\Buy;

use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProcessTelegramBuyRateOwnState
 * @package App\Telegram\Processes\ProcessState\Buy
 */
class ProcessTelegramBuyRateOwnState extends AbstractProcessTelegramState
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
                $this->updateUserState($user, config('states.buy-rate'));
                $responseMessage = $this->buildBuyConfirmMessage($user);

                break;
            case $messageText == __('telegram_buttons.backHome'):
                $this->updateUserState($user, null);
                $responseMessage = __('telegram.startMessage');

                break;
            case $messageText == (string)(float)$messageText:
                if ($messageText > 0) {
                    $this->updateUserState($user, config('states.buy-rate'));
                    $responseMessage = $this->buildBuyConfirmMessage($user, $messageText);
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
