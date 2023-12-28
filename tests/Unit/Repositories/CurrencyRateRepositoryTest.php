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

        $this->assertSame($secondUsd->getId(), $resultUsd->getId());
        $this->assertSame($secondEur->getId(), $resultEur->getId());
    }

    public function testGetLastTwoCurrencyRatesExists(): void
    {
        $currency = 'EUR';

        $this->fixturesHelper->createCurrencyRate($currency);
        $second = $this->fixturesHelper->createCurrencyRate($currency);
        $third = $this->fixturesHelper->createCurrencyRate($currency);

        $result = $this->currencyRateRepository->getLastTwoCurrencyRates($currency);

        $this->assertCount(2, $result);
        $this->assertSame($third->getId(), $result[0]->getId());
        $this->assertSame($second->getId(), $result[1]->getId());
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
        $currencyRate->setCreatedAt($this->carbon->subMonths(2));
        $currencyRate->save();

        // Newer than a month
        $this->fixturesHelper->createCurrencyRate($currency);
        $this->fixturesHelper->createCurrencyRate($currency);
        $this->fixturesHelper->createCurrencyRate($currency);

        $result = $this->currencyRateRepository->getCurrencyRatesOfLastMonth($currency);
        $this->assertCount(3, $result);
    }
}
