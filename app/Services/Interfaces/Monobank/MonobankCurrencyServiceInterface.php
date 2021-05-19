<?php

namespace App\Services\Interfaces\Monobank;

use App\Services\Interfaces\Models\CurrencyRateServiceInterface;
use GuzzleHttp\Client;

/**
 * Interface MonobankCurrencyServiceInterface
 * @package App\Services\Interfaces\Monobank
 */
interface MonobankCurrencyServiceInterface
{
    /**
     * MonobankCurrencyServiceInterface constructor.
     * @param Client $client
     * @param CurrencyRateServiceInterface $currencyRateService
     * @return void
     */
    public function __construct(Client $client, CurrencyRateServiceInterface $currencyRateService);

    /**
     * @return bool
     */
    public function updateCurrencyRates(): bool;
}
