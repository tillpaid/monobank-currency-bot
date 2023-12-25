<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState\Statistics;

use App\Models\CurrencyRate;
use App\Models\TelegramUser;
use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;
use Illuminate\Database\Eloquent\Model;

class ProcessTelegramStatisticsCurrencyState extends AbstractProcessTelegramState
{
    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        $messageTextLower = mb_strtolower($messageText);

        switch (true) {
            case in_array($messageTextLower, config('monobank.currencies'), true):
                $this->updateUserState($telegramUser, null);

                $rates = $this->currencyRateService->getCurrencyRatesOfLastMonth($messageTextLower);
                $ratesResponse = [];

                $ratesMinMax = [
                    'values' => ['buy' => ['min' => PHP_INT_MAX, 'max' => 0], 'sell' => ['min' => PHP_INT_MAX, 'max' => 0]],
                    'data' => ['buy' => ['min' => null, 'max' => null], 'sell' => ['min' => null, 'max' => null]],
                ];

                foreach ($rates as $rate) {
                    $date = $rate->created_at->format('Y-m-d');
                    $rateBuy = $this->telegramBotService->format($rate->buy, 2, false);
                    $rateSell = $this->telegramBotService->format($rate->sell, 2, false);

                    $ratesResponse[] = "`* {$date} - {$rateBuy}₴ / {$rateSell}₴`";

                    $this->processMinMaxRates($ratesMinMax, $rate, $date);
                }

                $ratesResponse = implode("\n", $ratesResponse);
                $responseMessage = __('telegram.statisticsCurrencyReport', [
                    'currencyUpper' => mb_strtoupper($messageText),
                    'ratesResponse' => $ratesResponse,
                    'buyMin' => $ratesMinMax['data']['buy']['min'],
                    'buyMax' => $ratesMinMax['data']['buy']['max'],
                    'sellMin' => $ratesMinMax['data']['sell']['min'],
                    'sellMax' => $ratesMinMax['data']['sell']['max'],
                ]);

                break;

            case $messageText === __('telegram_buttons.back'):
                $this->updateUserState($telegramUser, null);
                $responseMessage = __('telegram.startMessage');

                break;

            default:
                $responseMessage = __('telegram.currencyNotSupported');
        }

        return $responseMessage;
    }

    private function processMinMaxRates(array &$ratesMinMax, CurrencyRate $rate, string $date): void
    {
        $buyString = "{$date} - {$rate->buy}₴";
        $sellString = "{$date} - {$rate->sell}₴";

        if ($rate->buy <= $ratesMinMax['values']['buy']['min']) {
            $ratesMinMax['values']['buy']['min'] = $rate->buy;
            $ratesMinMax['data']['buy']['min'] = $buyString;
        }

        if ($rate->buy >= $ratesMinMax['values']['buy']['max']) {
            $ratesMinMax['values']['buy']['max'] = $rate->buy;
            $ratesMinMax['data']['buy']['max'] = $buyString;
        }

        if ($rate->sell <= $ratesMinMax['values']['sell']['min']) {
            $ratesMinMax['values']['sell']['min'] = $rate->sell;
            $ratesMinMax['data']['sell']['min'] = $sellString;
        }

        if ($rate->sell >= $ratesMinMax['values']['sell']['max']) {
            $ratesMinMax['values']['sell']['max'] = $rate->sell;
            $ratesMinMax['data']['sell']['max'] = $sellString;
        }
    }
}
