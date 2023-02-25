<?php

namespace App\Services\Monobank;

use App\Services\Interfaces\Models\CurrencyRateServiceInterface;
use App\Services\Interfaces\Monobank\MonobankCurrencyServiceInterface;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class MonobankCurrencyService implements MonobankCurrencyServiceInterface
{
    private Client $client;
    private CurrencyRateServiceInterface $currencyRateService;
    private int $uahCode;
    private array $currencyCodes;

    public function __construct(Client $client, CurrencyRateServiceInterface $currencyRateService)
    {
        $this->client = $client;
        $this->currencyRateService = $currencyRateService;

        $this->uahCode = config('monobank.uahCode');
        $this->currencyCodes = config('monobank.currencyCodes');
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

        try {
            $response = $this->client->get(config('monobank.monobank_currency_url'));

            if ($response->getStatusCode() == 200 && $response->getBody()) {
                $output = json_decode($response->getBody()->getContents(), true);
            }
        } catch (\Exception $exception) {
            Log::error('Monobank update process error');
            Log::error($exception);
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
            round($newRate['rateBuy'], 5) != round($rate->buy, 5) ||
            round($newRate['rateSell'], 5) != round($rate->sell, 5);
    }
}
