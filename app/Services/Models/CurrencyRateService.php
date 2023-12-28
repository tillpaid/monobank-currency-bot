<?php

declare(strict_types=1);

namespace App\Services\Models;

use App\Models\CurrencyRate;

class CurrencyRateService
{
    public function createCurrencyRate(string $currencyName, float $sell, float $buy): bool
    {
        $currencyRate = new CurrencyRate();
        // TODO: Why you call it name? It's not a name, it's a currency code
        $currencyRate->setCurrency($currencyName);
        $currencyRate->setSell($sell);
        $currencyRate->setBuy($buy);

        return $currencyRate->save();
    }
}
