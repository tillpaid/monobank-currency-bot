<?php

namespace App\Repositories;

use App\Models\CurrencyAccount;

class CurrencyAccountRepository implements Interfaces\CurrencyAccountRepositoryInterface
{
    private $model;

    public function __construct(CurrencyAccount $currencyAccount)
    {
        $this->model = $currencyAccount;
    }

    public function getUserCurrencySum(int $userId, string $currency): ?int
	{
	    return $this->model
            ->where('telegram_user_id', $userId)
            ->where('currency', $currency)
            ->sum('currency_value');
	}
}
