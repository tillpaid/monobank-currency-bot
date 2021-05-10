<?php

namespace App\Services\Models;

use App\Models\CurrencyAccount;
use App\Repositories\Interfaces\CurrencyAccountRepositoryInterface;
use App\Services\Interfaces\Models\CurrencyAccountServiceInterface;

class CurrencyAccountService implements CurrencyAccountServiceInterface
{
    private $currencyAccountRepository;

    public function __construct(CurrencyAccountRepositoryInterface $currencyAccountRepository)
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
            'currency_value'   => $uahValue / $purchaseRate,
        ]);

        return isset($currencyAccount->id);
    }

    public function getUserCurrencySum(int $userId, string $currency): ?int
    {
        return $this->currencyAccountRepository->getUserCurrencySum($userId, $currency);
    }
}
