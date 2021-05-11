<?php

namespace App\Services\Models;

use App\Models\CurrencyRate;
use App\Repositories\Interfaces\CurrencyRateRepositoryInterface;
use App\Services\Interfaces\Models\CurrencyRateServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CurrencyRateService implements CurrencyRateServiceInterface
{
    private $currencyRateRepository;

    public function __construct(CurrencyRateRepositoryInterface $currencyRateRepository)
    {
        $this->currencyRateRepository = $currencyRateRepository;
    }

    public function createCurrencyRate(string $currencyName, string $sell, string $buy): bool
    {
        $currency = CurrencyRate::create([
            'currency' => $currencyName,
            'sell'     => $sell,
            'buy'      => $buy,
        ]);

        return isset($currency->id);
    }

    public function getLatestCurrencyRate(string $currency): ?Model
    {
        return $this->currencyRateRepository->getLatestCurrencyRate($currency);
    }

    public function getLastTwoCurrencyRates(string $currency): ?Collection
    {
        return $this->currencyRateRepository->getLastTwoCurrencyRates($currency);
    }
}
