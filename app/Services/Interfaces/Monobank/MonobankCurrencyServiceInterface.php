<?php

namespace App\Services\Interfaces\Monobank;

use App\Services\Interfaces\Models\CurrencyRateServiceInterface;
use GuzzleHttp\Client;

interface MonobankCurrencyServiceInterface
{
    public function __construct(Client $client, CurrencyRateServiceInterface $currencyRateService);

    public function updateCurrencyRates(): bool;
}
