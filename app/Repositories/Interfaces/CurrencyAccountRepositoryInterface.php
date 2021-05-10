<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface CurrencyAccountRepositoryInterface
{
    public function getUserCurrencySum(int $userId, string $currency): ?int;

    public function getFirstUserCurrencyAccount(int $userId, string $currency): ?Model;
}
