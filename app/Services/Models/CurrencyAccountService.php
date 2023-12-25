<?php

declare(strict_types=1);

namespace App\Services\Models;

use App\Models\CurrencyAccount;
use App\Repositories\CurrencyAccountRepository;

class CurrencyAccountService
{
    private CurrencyAccountRepository $currencyAccountRepository;

    public function __construct(CurrencyAccountRepository $currencyAccountRepository)
    {
        $this->currencyAccountRepository = $currencyAccountRepository;
    }

    public function create(int $userId, string $currency, float $uahValue, float $purchaseRate): bool
    {
        $currencyAccount = new CurrencyAccount();
        $currencyAccount->telegram_user_id = $userId;
        $currencyAccount->currency = $currency;
        $currencyAccount->uah_value = $uahValue;
        $currencyAccount->purchase_rate = $purchaseRate;
        $currencyAccount->currency_value = round($uahValue / $purchaseRate, 5);

        return $currencyAccount->save();
    }

    public function getUserCurrencySum(int $userId, string $currency): ?float
    {
        return $this->currencyAccountRepository->getUserCurrencySum($userId, $currency);
    }

    public function getFirstUserCurrencyAccount(int $userId, string $currency): ?CurrencyAccount
    {
        return $this->currencyAccountRepository->getFirstUserCurrencyAccount($userId, $currency);
    }

    public function getLessProfitUserCurrencyAccount(int $userId, string $currency): ?CurrencyAccount
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
            }

            $currencySum -= $currencyAccount->currency_value;
            $currencyAccount->delete();
        }
    }

    public function getUserBalanceSum(int $userId): ?array
    {
        return $this->currencyAccountRepository->getUserBalanceSum($userId);
    }
}
