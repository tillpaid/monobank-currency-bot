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
        if ($this->isMessageTextCurrency($messageText)) {
            return $this->processAction($telegramUser, $messageText);
        }

        return match ($messageText) {
            __('telegram_buttons.back') => $this->processBackButton($telegramUser),
            default => __('telegram.currencyNotSupported'),
        };
    }

    private function processAction(TelegramUser $telegramUser, string $messageText): string
    {
        $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_DEFAULT);

        $currency = mb_strtolower($messageText);
        $rates = $this->currencyRateRepository->getCurrencyRatesOfLastMonth($currency);

        $data = [
            'currencyUpper' => mb_strtoupper($currency),
            'ratesResponse' => $this->resolveRatesFormattedResponse($rates),
        ];

        return __(
            'telegram.statisticsCurrencyReport',
            array_merge($data, $this->resolveMinMaxRates($rates))
        );
    }

    private function processBackButton(TelegramUser $telegramUser): string
    {
        $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_DEFAULT);

        return __('telegram.startMessage');
    }

    /**
     * @param CurrencyRate[] $rates
     */
    private function resolveRatesFormattedResponse(array $rates): string
    {
        $ratesResponse = [];

        foreach ($rates as $rate) {
            $date = $rate->getCreatedAt()->format('Y-m-d');
            $rateBuy = $this->telegramBotService->format($rate->getBuy(), 2, false);
            // TODO: Sell can be null. Or can't? Than change the field in the model
            $rateSell = $this->telegramBotService->format($rate->getSell(), 2, false);

            $ratesResponse[] = "`* {$date} - {$rateBuy}₴ / {$rateSell}₴`";
        }

        return implode("\n", $ratesResponse);
    }

    /**
     * @param CurrencyRate[] $rates
     */
    private function resolveMinMaxRates(array $rates): array
    {
        $output = ['buyMin' => '', 'buyMax' => '', 'sellMin' => '', 'sellMax' => ''];

        $minBuy = PHP_INT_MAX;
        $minSell = PHP_INT_MAX;
        $maxBuy = 0;
        $maxSell = 0;

        foreach ($rates as $rate) {
            $date = $rate->getCreatedAt()->format('Y-m-d');
            $buyString = sprintf('%s - %s₴', $date, $rate->getBuy());
            $sellString = sprintf('%s - %s₴', $date, $rate->getSell());

            if ($rate->getBuy() <= $minBuy) {
                $minBuy = $rate->getBuy();
                $output['buyMin'] = $buyString;
            }

            if ($rate->getBuy() >= $maxBuy) {
                $maxBuy = $rate->getBuy();
                $output['buyMax'] = $buyString;
            }

            if ($rate->getSell() <= $minSell) {
                $minSell = $rate->getSell();
                $output['sellMin'] = $sellString;
            }

            if ($rate->getSell() >= $maxSell) {
                $maxSell = $rate->getSell();
                $output['sellMax'] = $sellString;
            }
        }

        return $output;
    }

    private function isMessageTextCurrency(string $messageText): bool
    {
        $lowerMessageText = mb_strtolower($messageText);
        $currenciesList = config('monobank.currencies');

        return in_array($lowerMessageText, $currenciesList, true);
    }
}
