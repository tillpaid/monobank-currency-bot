<?php

namespace App\Telegram\Processes\ProcessState\Sell;

use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;
use Illuminate\Database\Eloquent\Model;

class ProcessTelegramSellState extends AbstractProcessTelegramState
{
    public function process(Model $user, string $messageText): string
    {
        switch (true) {
            case in_array($messageText, config('monobank.currencies')):
                $currencySum = $this->currencyAccountService->getUserCurrencySum($user->id, $messageText);

                if ($currencySum > 0) {
                    $responseMessage = __('telegram.sellSum', ['currencySum' => $currencySum, 'currency' => $messageText]);
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
