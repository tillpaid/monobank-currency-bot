<?php

namespace App\Telegram\Processes\ProcessState\Statistics;

use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;
use Illuminate\Database\Eloquent\Model;

class ProcessTelegramStatisticsCurrencyState extends AbstractProcessTelegramState
{
    /**
     * @param Model $user
     * @param string $messageText
     * @return string
     */
    public function process(Model $user, string $messageText): string
    {
        switch (true) {
            case in_array($messageText, config('monobank.currencies')):
                $this->updateUserState($user, null);

                $rates = $this->currencyRateService->getCurrencyRatesOfLastMonth($messageText);
                $ratesResponse = [];

                foreach ($rates as $key => $rate) {
                    $number = $key + 1;
                    $date = $rate->created_at->format('Y-m-d H:i:s');

                    $ratesResponse[] = "{$number}. {$date} - {$rate->buy}₴ / {$rate->sell}₴";
                }

                $currencyUpper = mb_strtoupper($messageText);
                $ratesResponse = join("\n", $ratesResponse);
                $responseMessage = __('telegram.statisticsCurrencyReport', compact('currencyUpper', 'ratesResponse'));

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
