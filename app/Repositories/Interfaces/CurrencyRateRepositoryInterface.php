<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface CurrencyRateRepositoryInterface
{
    public function getLatestCurrencyRate(string $currency): ?Model;

    public function getLastTwoCurrencyRates(string $currency): ?Collection;
}
