<?php

namespace App\Services;

use App\Services\Interfaces\CurrencyRateServiceInterface;
use App\Services\Interfaces\MonobankCurrencyServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class MonobankCurrencyService implements MonobankCurrencyServiceInterface
{
    private $currencyRateService;

    private $uahCode;
    private $currencyCodes;

    public function __construct(CurrencyRateServiceInterface $currencyRateService)
    {
        $this->currencyRateService = $currencyRateService;

        $this->uahCode = config('app.uahCode');
        $this->currencyCodes = config('app.currencyCodes');
    }

    public function updateCurrencyRates(): bool
    {
        $newRates = $this->getCurrency();
        $changed = $this->processNewRates($newRates);

        return $changed;
    }

    private function getCurrency(): array
    {
        $output = [];
        $response = Http::get(config('app.monobank_currency_url'));

        if ($response->status() == 200 && $response->body()) {
            $output = json_decode($response->body(), true);
        }

        return $output;
    }

    private function processNewRates(array $newRates): bool
    {
        $changed = false;

        foreach ($newRates as $newRate) {
            if (!$this->isItNeedleRate($newRate)) continue;

            $currencyName = $this->currencyCodes[$newRate['currencyCodeA']] ?? null;
            $rate = $this->currencyRateService->getLatestCurrencyRate($currencyName);

            if ($this->isRateDifferent($rate, $newRate)) {
                $this->currencyRateService->createCurrencyRate($currencyName, $newRate['rateSell'], $newRate['rateBuy']);
                $changed = true;
            }
        }

        return $changed;
    }

    private function isItNeedleRate(array $newRate): bool
    {
        $needle = true;

        if ($newRate['currencyCodeB'] != $this->uahCode) {
            $needle = false;
        } else if (!in_array($newRate['currencyCodeA'], array_keys($this->currencyCodes))) {
            $needle = false;
        }

        return $needle;
    }

    private function isRateDifferent(?Model $rate, array $newRate): bool
    {
        return
            is_null($rate) ||
            $newRate['rateBuy'] != $rate->buy ||
            $newRate['rateSell'] != $rate->sell;
    }
}
