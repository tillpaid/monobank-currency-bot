<?php

namespace App\Services\Models;

use App\Models\CurrencyAccount;
use App\Repositories\CurrencyAccountRepository;
use Illuminate\Database\Eloquent\Model;

class CurrencyAccountService
{
    private CurrencyAccountRepository $currencyAccountRepository;

    public function __construct(CurrencyAccountRepository $currencyAccountRepository)
    {
        $this->currencyAccountRepository = $currencyAccountRepository;
    }

    public function create(int $userId, string $currency, float $uahValue, float $purchaseRate): bool
    {
        $currencyAccount = CurrencyAccount::create([
            'telegram_user_id' => $userId,
            'currency'         => $currency,
            'uah_value'        => $uahValue,
            'purchase_rate'    => $purchaseRate,
            'currency_value'   => round($uahValue / $purchaseRate, 5),
        ]);

        return isset($currencyAccount->id);
    }

    public function getUserCurrencySum(int $userId, string $currency): ?float
    {
        return $this->currencyAccountRepository->getUserCurrencySum($userId, $currency);
    }

    public function getFirstUserCurrencyAccount(int $userId, string $currency): ?Model
    {
        return $this->currencyAccountRepository->getFirstUserCurrencyAccount($userId, $currency);
    }

    public function getLessProfitUserCurrencyAccount(int $userId, string $currency): ?Model
    {
        return $this->currencyAccountRepository->getLessProfitUserCurrencyAccount($userId, $currency);
    }

    public function sellCurrency(int $userId, string $currency, float $currencySum): void
    {
        while ($currencySum > 0) {
            $currencySum = round($currencySum, 5);

            if (!$currencyAccount = $this->getLessProfitUserCurrencyAccount($userId, $currency)) {
                break;
            }

            if ($currencyAccount->currency_value > $currencySum) {
                $currencyAccount->currency_value -= $currencySum;
                $currencyAccount->uah_value = $currencyAccount->currency_value * $currencyAccount->purchase_rate;
                $currencyAccount->save();

                break;
            } else {
                $currencySum -= $currencyAccount->currency_value;
                $currencyAccount->delete();
            }
        }
    }

    public function getUserBalanceSum(int $userId): ?array
    {
        return $this->currencyAccountRepository->getUserBalanceSum($userId);
    }
}
