<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface CurrencyRateRepositoryInterface
{
    public function getLatestCurrencyRate(string $currency): ?Model;
}
