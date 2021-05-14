<?php

namespace App\Services\Models;

use App\Models\CurrencyRate;
use App\Repositories\Interfaces\CurrencyRateRepositoryInterface;
use App\Services\Interfaces\Models\CurrencyRateServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CurrencyRateService
 * @package App\Services\Models
 */
class CurrencyRateService implements CurrencyRateServiceInterface
{
    /**
     * @var CurrencyRateRepositoryInterface
     */
    private $currencyRateRepository;

    /**
     * CurrencyRateService constructor.
     * @param CurrencyRateRepositoryInterface $currencyRateRepository
     */
    public function __construct(CurrencyRateRepositoryInterface $currencyRateRepository)
    {
        $this->currencyRateRepository = $currencyRateRepository;
    }

    /**
     * @param string $currencyName
     * @param string $sell
     * @param string $buy
     * @return bool
     */
    public function createCurrencyRate(string $currencyName, string $sell, string $buy): bool
    {
        $currency = CurrencyRate::create([
            'currency' => $currencyName,
            'sell'     => $sell,
            'buy'      => $buy,
        ]);

        return isset($currency->id);
    }

    /**
     * @param string $currency
     * @return Model|null
     */
    public function getLatestCurrencyRate(string $currency): ?Model
    {
        return $this->currencyRateRepository->getLatestCurrencyRate($currency);
    }

    /**
     * @param string $currency
     * @return Collection|null
     */
    public function getLastTwoCurrencyRates(string $currency): ?Collection
    {
        return $this->currencyRateRepository->getLastTwoCurrencyRates($currency);
    }
}
