<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\CurrencyRate;
use Carbon\Carbon;

class CurrencyRateRepository
{
    private CurrencyRate $currencyRate;
    private Carbon $carbon;

    public function __construct(CurrencyRate $currencyRate, Carbon $carbon)
    {
        $this->currencyRate = $currencyRate;
        $this->carbon = $carbon;
    }

    public function getLatestCurrencyRate(string $currency): ?CurrencyRate
    {
        return $this->currencyRate
            ->newQuery()
            ->where('currency', $currency)
            ->latest('id')
            ->get()
            ->first()
        ;
    }

    /**
     * @return null|CurrencyRate[]
     */
    public function getLastTwoCurrencyRates(string $currency): ?array
    {
        $rates = $this->currencyRate
            ->newQuery()
            ->where('currency', $currency)
            ->orderBy('id', 'DESC')
            ->take(2)
            ->get()
            ->all()
        ;

        return 2 === count($rates) ? $rates : null;
    }

    /**
     * @return CurrencyRate[]
     */
    public function getCurrencyRatesOfLastMonth(string $currency): array
    {
        $startDate = $this->carbon->subMonth()->format('Y-m-d H:i:s');

        return $this->currencyRate
            ->newQuery()
            ->where('currency', $currency)
            ->where('created_at', '>=', $startDate)
            ->get()
            ->all()
        ;
    }
}
