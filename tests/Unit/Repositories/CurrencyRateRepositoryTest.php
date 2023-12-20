<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Repositories\CurrencyRateRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;

class CurrencyRateRepositoryTest extends TestCase
{
    private CurrencyRateRepository $currencyRateRepository;

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->currencyRateRepository = $this->app->make(CurrencyRateRepository::class);
    }

    public function testGetLatestCurrencyRate(): void
    {
        $currencyUsd = 'USD';
        $currencyEur = 'EUR';

        $this->fixturesHelper->createCurrencyRate($currencyUsd);
        $this->fixturesHelper->createCurrencyRate($currencyEur);
        $secondUsd = $this->fixturesHelper->createCurrencyRate($currencyUsd);
        $secondEur = $this->fixturesHelper->createCurrencyRate($currencyEur);

        $resultUsd = $this->currencyRateRepository->getLatestCurrencyRate($currencyUsd);
        $resultEur = $this->currencyRateRepository->getLatestCurrencyRate($currencyEur);

        $this->assertEquals($secondUsd->id, $resultUsd->id);
        $this->assertEquals($secondEur->id, $resultEur->id);
    }

    public function testGetLastTwoCurrencyRatesExists(): void
    {
        $currency = 'EUR';

        $this->fixturesHelper->createCurrencyRate($currency);
        $second = $this->fixturesHelper->createCurrencyRate($currency);
        $third = $this->fixturesHelper->createCurrencyRate($currency);

        $result = $this->currencyRateRepository->getLastTwoCurrencyRates($currency);

        $this->assertCount(2, $result);
        $this->assertEquals($third->id, $result[0]->id);
        $this->assertEquals($second->id, $result[1]->id);
    }

    public function testGetLastTwoCurrencyRatesOnlyOneExists(): void
    {
        $currency = 'EUR';
        $this->fixturesHelper->createCurrencyRate($currency);

        $result = $this->currencyRateRepository->getLastTwoCurrencyRates($currency);
        $this->assertNull($result);
    }

    public function testGetLastTwoCurrencyRatesNotExist(): void
    {
        $currency = 'EUR';

        $result = $this->currencyRateRepository->getLastTwoCurrencyRates($currency);
        $this->assertNull($result);
    }

    public function testGetCurrencyRatesOfLastMonth(): void
    {
        $currency = 'EUR';

        // Older than a month
        $currencyRate = $this->fixturesHelper->createCurrencyRate($currency);
        $currencyRate->created_at = $this->carbon->subMonths(2)->format('Y-m-d H:i:s');
        $currencyRate->save();

        // Newer than a month
        $this->fixturesHelper->createCurrencyRate($currency);
        $this->fixturesHelper->createCurrencyRate($currency);
        $this->fixturesHelper->createCurrencyRate($currency);

        $result = $this->currencyRateRepository->getCurrencyRatesOfLastMonth($currency);
        $this->assertCount(3, $result);
    }
}
