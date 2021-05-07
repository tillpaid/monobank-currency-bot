<?php

namespace App\Services\Interfaces;

interface MonobankCurrencyServiceInterface
{
    public function updateCurrencyRates(): bool;
}
