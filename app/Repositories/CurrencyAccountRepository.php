<?php

namespace App\Repositories;

use App\Models\CurrencyAccount;
use App\Repositories\Interfaces\CurrencyAccountRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class CurrencyAccountRepository
 * @package App\Repositories
 */
class CurrencyAccountRepository implements CurrencyAccountRepositoryInterface
{
    /**
     * @var CurrencyAccount
     */
    private $model;

    /**
     * CurrencyAccountRepository constructor.
     * @param CurrencyAccount $currencyAccount
     */
    public function __construct(CurrencyAccount $currencyAccount)
    {
        $this->model = $currencyAccount;
    }

    /**
     * @param int $userId
     * @param string $currency
     * @return float|null
     */
    public function getUserCurrencySum(int $userId, string $currency): ?float
    {
        return $this->model
            ->where('telegram_user_id', $userId)
            ->where('currency', $currency)
            ->sum('currency_value');
    }

    /**
     * @param int $userId
     * @param string $currency
     * @return Model|null
     */
    public function getFirstUserCurrencyAccount(int $userId, string $currency): ?Model
    {
        return $this->model
            ->where('telegram_user_id', $userId)
            ->where('currency', $currency)
            ->first();
    }

    /**
     * @param int $userId
     * @return array|null
     */
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
