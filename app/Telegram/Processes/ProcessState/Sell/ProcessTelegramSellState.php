<?php

namespace App\Telegram\Processes\ProcessState\Sell;

use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProcessTelegramSellState
 * @package App\Telegram\Processes\ProcessState\Sell
 */
class ProcessTelegramSellState extends AbstractProcessTelegramState
{
    /**
     * @param Model $user
     * @param string $messageText
     * @return string
     */
    public function process(Model $user, string $messageText): string
    {
        $messageTextLower = mb_strtolower($messageText);

        switch (true) {
            case in_array($messageTextLower, config('monobank.currencies')):
                $currencySum = $this->currencyAccountService->getUserCurrencySum($user->id, $messageTextLower);

                if ($currencySum > 0) {
                    $this->updateUserState($user, config('states.sell-sum'), ['sell-currency' => $messageTextLower, 'sell-currency-sum-all' => $currencySum]);

                    $currencySum = number_format($currencySum, 5, '.', ' ');
                    $responseMessage = __('telegram.sellSum', ['currencySum' => $currencySum, 'currency' => $messageTextLower]);
                } else {
                    $responseMessage = __('telegram.sellEmptySum');
                }

                break;
            case $messageText == __('telegram_buttons.back'):
                $this->updateUserState($user, null);
                $responseMessage = __('telegram.startMessage');

                break;
            default:
                $responseMessage = __('telegram.currencyNotSupported');
        }

        return $responseMessage;
    }
}
