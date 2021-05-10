<?php

namespace App\Services\Models;

use App\Models\CurrencyAccount;
use App\Services\Interfaces\Models\CurrencyAccountServiceInterface;

class CurrencyAccountService implements CurrencyAccountServiceInterface
{
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
}
