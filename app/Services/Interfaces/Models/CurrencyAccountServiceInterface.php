<?php

namespace App\Services\Interfaces\Models;

interface CurrencyAccountServiceInterface
{
    public function create(int $userId, string $currency, float $uahValue, float $purchaseRate): bool;
}