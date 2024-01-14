<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState\Statistics;

use App\Models\CurrencyRate;
use App\Models\TelegramUser;
use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;

class ProcessTelegramStatisticsCurrencyState extends AbstractProcessTelegramState
{
    public function getState(): ?string
    {
        return TelegramUser::STATE_STATISTICS_CURRENCY;
    }

    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        $messageTextLower = mb_strtolower($messageText);

        switch (true) {
            case in_array($messageTextLower, config('monobank.currencies'), true):
                $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_DEFAULT);

                $rates = $this->currencyRateRepository->getCurrencyRatesOfLastMonth($messageTextLower);
                $ratesResponse = [];

                $ratesMinMax = [
                    'values' => ['buy' => ['min' => PHP_INT_MAX, 'max' => 0], 'sell' => ['min' => PHP_INT_MAX, 'max' => 0]],
                    'data' => ['buy' => ['min' => null, 'max' => null], 'sell' => ['min' => null, 'max' => null]],
                ];

                foreach ($rates as $rate) {
                    $date = $rate->getCreatedAt()->format('Y-m-d');
                    $rateBuy = $this->telegramBotService->format($rate->getBuy(), 2, false);
                    // TODO: Sell can be null. Or can't? Than change the field in the model
                    $rateSell = $this->telegramBotService->format($rate->getSell(), 2, false);

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
                $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_DEFAULT);
                $responseMessage = __('telegram.startMessage');

                break;

            default:
                $responseMessage = __('telegram.currencyNotSupported');
        }

        return $responseMessage;
    }

    /**
     * @param array<string, array<string, array<string, null|int>>> $ratesMinMax
     */
    private function processMinMaxRates(array &$ratesMinMax, CurrencyRate $rate, string $date): void
    {
        $buyString = "{$date} - {$rate->getBuy()}₴";
        $sellString = "{$date} - {$rate->getSell()}₴";

        if ($rate->getBuy() <= $ratesMinMax['values']['buy']['min']) {
            $ratesMinMax['values']['buy']['min'] = $rate->getBuy();
            $ratesMinMax['data']['buy']['min'] = $buyString;
        }

        if ($rate->getBuy() >= $ratesMinMax['values']['buy']['max']) {
            $ratesMinMax['values']['buy']['max'] = $rate->getBuy();
            $ratesMinMax['data']['buy']['max'] = $buyString;
        }

        if ($rate->getSell() <= $ratesMinMax['values']['sell']['min']) {
            $ratesMinMax['values']['sell']['min'] = $rate->getSell();
            $ratesMinMax['data']['sell']['min'] = $sellString;
        }

        if ($rate->getSell() >= $ratesMinMax['values']['sell']['max']) {
            $ratesMinMax['values']['sell']['max'] = $rate->getSell();
            $ratesMinMax['data']['sell']['max'] = $sellString;
        }
    }
}
