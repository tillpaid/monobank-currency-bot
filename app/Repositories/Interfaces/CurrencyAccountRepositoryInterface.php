<?php

namespace App\Repositories\Interfaces;

interface CurrencyAccountRepositoryInterface
{
    public function getUserCurrencySum(int $userId, string $currency): ?int;
}
