<?php

namespace App\Services\Interfaces\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface CurrencyRateServiceInterface
{
    public function createCurrencyRate(string $currencyName, string $sell, string $buy): bool;

    public function getLatestCurrencyRate(string $currency): ?Model;

    public function getLastTwoCurrencyRates(string $currency): ?Collection;

    public function getCurrencyRatesOfLastMonth(string $currency): ?Collection;
}
