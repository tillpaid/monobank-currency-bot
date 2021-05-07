<?php

namespace App\Repositories;

use App\Models\CurrencyRate;
use App\Repositories\Interfaces\CurrencyRateRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class CurrencyRateRepository implements CurrencyRateRepositoryInterface
{
    private $model;

    public function __construct(CurrencyRate $currencyRate)
    {
        $this->model = $currencyRate;
    }

    public function getLatestCurrencyRate(string $currency): ?Model
    {
        return $this->model
            ->where('currency', $currency)
            ->latest('id')
            ->first();
    }
}
