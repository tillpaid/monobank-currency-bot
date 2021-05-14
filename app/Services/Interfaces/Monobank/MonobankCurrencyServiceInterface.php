<?php

namespace App\Services\Interfaces\Monobank;

/**
 * Interface MonobankCurrencyServiceInterface
 * @package App\Services\Interfaces\Monobank
 */
interface MonobankCurrencyServiceInterface
{
    /**
     * @return bool
     */
    public function updateCurrencyRates(): bool;
}
