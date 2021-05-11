<?php

namespace App\Services\Interfaces\Models;

use Illuminate\Database\Eloquent\Model;

interface CurrencyAccountServiceInterface
{
    public function create(int $userId, string $currency, float $uahValue, float $purchaseRate): bool;

    public function getUserCurrencySum(int $userId, string $currency): ?float;

    public function getFirstUserCurrencyAccount(int $userId, string $currency): ?Model;

    public function sellCurrency(int $userId, string $currency, float $currencySum): void;

    public function getUserBalanceSum(int $userId): ?array;
}
