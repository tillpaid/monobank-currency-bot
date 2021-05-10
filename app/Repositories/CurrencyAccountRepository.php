<?php

namespace App\Repositories;

use App\Models\CurrencyAccount;
use App\Repositories\Interfaces\CurrencyAccountRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class CurrencyAccountRepository implements CurrencyAccountRepositoryInterface
{
    private $model;

    public function __construct(CurrencyAccount $currencyAccount)
    {
        $this->model = $currencyAccount;
    }

    public function getUserCurrencySum(int $userId, string $currency): ?float
    {
        return $this->model
            ->where('telegram_user_id', $userId)
            ->where('currency', $currency)
            ->sum('currency_value');
    }

    public function getFirstUserCurrencyAccount(int $userId, string $currency): ?Model
    {
        return $this->model
            ->where('telegram_user_id', $userId)
            ->where('currency', $currency)
            ->first();
    }
}
