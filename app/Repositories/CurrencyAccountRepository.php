<?php

namespace App\Repositories;

use App\Models\CurrencyAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CurrencyAccountRepository
{
    private CurrencyAccount $model;

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

    public function getLessProfitUserCurrencyAccount(int $userId, string $currency): ?Model
    {
        return $this->model
            ->where('telegram_user_id', $userId)
            ->where('currency', $currency)
            ->orderBy('purchase_rate', 'DESC')
            ->first();
    }

    public function getUserBalanceSum(int $userId): ?array
    {
        $output = [];
        $collection = $this->model
            ->select(
                'currency',
                DB::raw('SUM(currency_value) as currency_value'),
                DB::raw('SUM(uah_value) as uah_value')
            )
            ->where('telegram_user_id', $userId)
            ->groupBy('currency')
            ->get();

        foreach ($collection as $item) {
            $output[$item->currency] = [
                'currency_value' => $item->currency_value,
                'uah_value'      => $item->uah_value,
            ];
        }

        return $output;
    }
}
