<?php

declare(strict_types=1);

namespace App\Services\Monobank;

use App\Models\CurrencyRate;
use App\Repositories\CurrencyRateRepository;
use App\Services\Models\CurrencyRateService;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class MonobankCurrencyService
{
    private int $uahCode;
    private array $currencyCodes;

    public function __construct(
        private readonly Client $client,
        private readonly CurrencyRateService $currencyRateService,
        private readonly CurrencyRateRepository $currencyRateRepository,
    ) {
        $this->uahCode = config('monobank.uahCode');
        $this->currencyCodes = config('monobank.currencyCodes');
    }

    public function updateCurrencyRates(): bool
    {
        $newRates = $this->getCurrency();

        return $this->processNewRates($newRates);
    }

    private function getCurrency(): array
    {
        $output = [];

        try {
            $response = $this->client->get(config('monobank.monobank_currency_url'));

            if ($response->getStatusCode() === 200) {
                // TODO: Validate that we have a valid JSON
                $output = json_decode($response->getBody()->getContents(), true);
            }
        } catch (Exception $exception) {
            Log::error(sprintf('Monobank update process error: %s', $exception->getMessage()), [$exception]);
        }

        return $output;
    }

    private function processNewRates(array $newRates): bool
    {
        $changed = false;

        foreach ($newRates as $newRate) {
            if (!$this->isItNeedleRate($newRate)) {
                continue;
            }

            $currencyName = $this->currencyCodes[$newRate['currencyCodeA']] ?? null;
            $rate = $this->currencyRateRepository->getLatestCurrencyRate($currencyName);

            if ($this->isRateDifferent($rate, $newRate)) {
                $this->currencyRateService->createCurrencyRate($currencyName, (float) $newRate['rateSell'], (float) $newRate['rateBuy']);
                $changed = true;
            }
        }

        return $changed;
    }

    private function isItNeedleRate(array $newRate): bool
    {
        return $newRate['currencyCodeB'] === $this->uahCode
            && in_array($newRate['currencyCodeA'], array_keys($this->currencyCodes), true);
    }

    private function isRateDifferent(?CurrencyRate $rate, array $newRate): bool
    {
        return
            $rate === null
            || round($newRate['rateBuy'], 5) !== round($rate->getBuy(), 5)
            || round($newRate['rateSell'], 5) !== round($rate->getSell(), 5);
    }
}
