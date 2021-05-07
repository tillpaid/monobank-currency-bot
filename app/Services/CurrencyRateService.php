<?php

namespace App\Services;

use App\Models\CurrencyRate;
use App\Repositories\Interfaces\CurrencyRateRepositoryInterface;
use App\Services\Interfaces\CurrencyRateServiceInterface;
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
}
