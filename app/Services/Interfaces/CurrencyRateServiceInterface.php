<?php

namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface CurrencyRateServiceInterface
{
    public function createCurrencyRate(string $currencyName, string $sell, string $buy): bool;

    public function getLatestCurrencyRate(string $currency): ?Model;
}
