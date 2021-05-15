<?php

namespace App\Services\Models;

use App\Models\CurrencyAccount;
use App\Repositories\Interfaces\CurrencyAccountRepositoryInterface;
use App\Services\Interfaces\Models\CurrencyAccountServiceInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CurrencyAccountService
 * @package App\Services\Models
 */
class CurrencyAccountService implements CurrencyAccountServiceInterface
{
    /**
     * @var CurrencyAccountRepositoryInterface
     */
    private $currencyAccountRepository;

    /**
     * CurrencyAccountService constructor.
     * @param CurrencyAccountRepositoryInterface $currencyAccountRepository
     */
    public function __construct(CurrencyAccountRepositoryInterface $currencyAccountRepository)
    {
        $this->currencyAccountRepository = $currencyAccountRepository;
    }

    /**
     * @param int $userId
     * @param string $currency
     * @param float $uahValue
     * @param float $purchaseRate
     * @return bool
     */
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

    /**
     * @param int $userId
     * @param string $currency
     * @return float|null
     */
    public function getUserCurrencySum(int $userId, string $currency): ?float
    {
        return $this->currencyAccountRepository->getUserCurrencySum($userId, $currency);
    }

    /**
     * @param int $userId
     * @param string $currency
     * @return Model|null
     */
    public function getFirstUserCurrencyAccount(int $userId, string $currency): ?Model
    {
        return $this->currencyAccountRepository->getFirstUserCurrencyAccount($userId, $currency);
    }

    /**
     * @param int $userId
     * @param string $currency
     * @param float $currencySum
     * @return void
     */
    public function sellCurrency(int $userId, string $currency, float $currencySum): void
    {
        while ($currencySum > 0) {
            if (!$currencyAccount = $this->getFirstUserCurrencyAccount($userId, $currency)) {
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

    /**
     * @param int $userId
     * @return array|null
     */
    public function getUserBalanceSum(int $userId): ?array
    {
        return $this->currencyAccountRepository->getUserBalanceSum($userId);
    }
}
