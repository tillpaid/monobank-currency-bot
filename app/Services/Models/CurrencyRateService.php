<?php

declare(strict_types=1);

namespace App\Services\Models;

use App\Models\CurrencyRate;
use App\Repositories\CurrencyRateRepository;
use Illuminate\Database\Eloquent\Collection;

class CurrencyRateService
{
    private CurrencyRateRepository $currencyRateRepository;

    public function __construct(CurrencyRateRepository $currencyRateRepository)
    {
        $this->currencyRateRepository = $currencyRateRepository;
    }

    public function createCurrencyRate(string $currencyName, float $sell, float $buy): bool
    {
        $currencyRate = new CurrencyRate();
        $currencyRate->currency = $currencyName;
        $currencyRate->sell = $sell;
        $currencyRate->buy = $buy;

        return $currencyRate->save();
    }

    public function getLatestCurrencyRate(string $currency): ?CurrencyRate
    {
        return $this->currencyRateRepository->getLatestCurrencyRate($currency);
    }

    public function getLastTwoCurrencyRates(string $currency): ?Collection
    {
        return $this->currencyRateRepository->getLastTwoCurrencyRates($currency);
    }

    public function getCurrencyRatesOfLastMonth(string $currency): ?Collection
    {
        return $this->currencyRateRepository->getCurrencyRatesOfLastMonth($currency);
    }
}
