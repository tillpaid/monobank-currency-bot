<?php

namespace App\Services\Interfaces\Monobank;

interface MonobankCurrencyServiceInterface
{
    public function updateCurrencyRates(): bool;
}
