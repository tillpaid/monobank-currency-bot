<?php

declare(strict_types=1);

namespace App\Services\Models;

use App\Models\CurrencyAccount;
use App\Repositories\CurrencyAccountRepository;

class CurrencyAccountService
{
    public function __construct(
        private CurrencyAccountRepository $currencyAccountRepository,
    ) {
    }

    public function create(int $userId, string $currency, float $uahValue, float $purchaseRate): bool
    {
        $currencyAccount = new CurrencyAccount();
        // TODO: Change on Associate. You have an example already in your code.
        $currencyAccount->setTelegramUserId($userId);
        $currencyAccount->setCurrency($currency);
        $currencyAccount->setUahValue($uahValue);
        $currencyAccount->setPurchaseRate($purchaseRate);
        $currencyAccount->setCurrencyValue(round($uahValue / $purchaseRate, 5));

        return $currencyAccount->save();
    }

    public function sellCurrency(int $userId, string $currency, float $currencySum): void
    {
        while ($currencySum > 0) {
            $currencySum = round($currencySum, 5);

            $currencyAccount = $this->currencyAccountRepository->getLessProfitUserCurrencyAccount($userId, $currency);
            if (null === $currencyAccount) {
                break;
            }

            if ($currencyAccount->getCurrencyValue() > $currencySum) {
                // TODO: Refactor this place.
                $currencyAccount->setCurrencyValue($currencyAccount->getCurrencyValue() - $currencySum);
                $currencyAccount->setUahValue($currencyAccount->getCurrencyValue() * $currencyAccount->getPurchaseRate());
                $currencyAccount->save();

                break;
            }

            $currencySum -= $currencyAccount->getCurrencyValue();
            $currencyAccount->delete();
        }
    }
}
