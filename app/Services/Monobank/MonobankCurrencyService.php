<?php

namespace App\Services\Monobank;

use App\Services\Interfaces\Models\CurrencyRateServiceInterface;
use App\Services\Interfaces\Monobank\MonobankCurrencyServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

/**
 * Class MonobankCurrencyService
 * @package App\Services\Monobank
 */
class MonobankCurrencyService implements MonobankCurrencyServiceInterface
{
    /**
     * @var CurrencyRateServiceInterface
     */
    private $currencyRateService;

    /**
     * @var int
     */
    private $uahCode;
    /**
     * @var array
     */
    private $currencyCodes;

    /**
     * MonobankCurrencyService constructor.
     * @param CurrencyRateServiceInterface $currencyRateService
     */
    public function __construct(CurrencyRateServiceInterface $currencyRateService)
    {
        $this->currencyRateService = $currencyRateService;

        $this->uahCode = config('monobank.uahCode');
        $this->currencyCodes = config('monobank.currencyCodes');
    }

    /**
     * @return bool
     */
    public function updateCurrencyRates(): bool
    {
        $newRates = $this->getCurrency();
        $changed = $this->processNewRates($newRates);

        return $changed;
    }

    /**
     * @return array
     */
    private function getCurrency(): array
    {
        $output = [];
        $response = Http::get(config('monobank.monobank_currency_url'));

        if ($response->status() == 200 && $response->body()) {
            $output = json_decode($response->body(), true);
        }

        return $output;
    }

    /**
     * @param array $newRates
     * @return bool
     */
    private function processNewRates(array $newRates): bool
    {
        $changed = false;

        foreach ($newRates as $newRate) {
            if (!$this->isItNeedleRate($newRate)) continue;

            $currencyName = $this->currencyCodes[$newRate['currencyCodeA']] ?? null;
            $rate = $this->currencyRateService->getLatestCurrencyRate($currencyName);

            if ($this->isRateDifferent($rate, $newRate)) {
                $this->currencyRateService->createCurrencyRate($currencyName, $newRate['rateSell'], $newRate['rateBuy']);
                $changed = true;
            }
        }

        return $changed;
    }

    /**
     * @param array $newRate
     * @return bool
     */
    private function isItNeedleRate(array $newRate): bool
    {
        $needle = true;

        if ($newRate['currencyCodeB'] != $this->uahCode) {
            $needle = false;
        } else if (!in_array($newRate['currencyCodeA'], array_keys($this->currencyCodes))) {
            $needle = false;
        }

        return $needle;
    }

    /**
     * @param Model|null $rate
     * @param array $newRate
     * @return bool
     */
    private function isRateDifferent(?Model $rate, array $newRate): bool
    {
        return
            is_null($rate) ||
            $newRate['rateBuy'] != $rate->buy ||
            $newRate['rateSell'] != $rate->sell;
    }
}
