<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Models;

use App\Models\CurrencyRate;
use App\Repositories\CurrencyRateRepository;
use App\Services\Models\CurrencyRateService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;

class CurrencyRateServiceTest extends TestCase
{
    private CurrencyRateRepository $currencyRateRepository;
    private CurrencyRateService $currencyRateService;

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->currencyRateRepository = $this->app->make(CurrencyRateRepository::class);
        $this->currencyRateService = $this->app->make(CurrencyRateService::class);
    }

    public function testCreateCurrencyRate(): void
    {
        $currency = 'EUR';
        $sell = 39.12;
        $buy = 41.38;

        $result = $this->currencyRateService->createCurrencyRate($currency, $sell, $buy);
        $this->assertTrue($result);

        $currencyRate = $this->currencyRateRepository->getLatestCurrencyRate($currency);
        $this->assertSame($currency, $currencyRate->currency);
        $this->assertSame($sell, $currencyRate->sell);
        $this->assertSame($buy, $currencyRate->buy);
    }

    public function testGetLatestCurrencyRate(): void
    {
        $currencies = config('monobank.currencies');

        // Check on null
        CurrencyRate::query()->truncate();
        $currencyName = $currencies[0];
        $result = $this->currencyRateService->getLatestCurrencyRate($currencyName);
        $this->assertNull($result);

        // Check when result is model
        foreach ($currencies as $currencyName) {
            $counter = 3;

            while ($counter-- > 0) {
                $expected = CurrencyRate
                    ::factory(1)
                        ->create(['currency' => $currencyName])
                        ->first()
                ;
                $result = $this->currencyRateService->getLatestCurrencyRate($currencyName);

                $this->assertSame($expected->id, $result->id);
            }
        }
    }

    public function testGetLastTwoCurrencyRates(): void
    {
        $currencies = config('monobank.currencies');
        $currencyName = $currencies[0];

        // Check on null after truncate
        CurrencyRate::query()->truncate();
        $result = $this->currencyRateService->getLastTwoCurrencyRates($currencyName);
        $this->assertNull($result);

        // Check on null when isset one rate row
        CurrencyRate::factory(1)->create(['currency' => $currencyName]);
        $result = $this->currencyRateService->getLastTwoCurrencyRates($currencyName);
        $this->assertNull($result);

        // Check when result is Collection
        foreach ($currencies as $currencyName) {
            $counter = 3;

            while ($counter-- > 0) {
                $expected = CurrencyRate
                    ::factory(2)
                        ->create(['currency' => $currencyName])
                ;
                $expected = array_reverse($expected->toArray());
                $result = $this->currencyRateService->getLastTwoCurrencyRates($currencyName);

                $this->assertCount(2, $result);
                $this->assertSame($expected[0]['id'], $result[0]->id);
            }
        }
    }

    public function testGetCurrencyRatesOfLastMonth(): void
    {
        $currencies = config('monobank.currencies');
        CurrencyRate::query()->truncate();

        // Check when result is Collection
        foreach ($currencies as $currencyName) {
            $expected = CurrencyRate::factory(5)
                ->create(['currency' => $currencyName])
                ->map(fn ($item) => $item->id)
                ->all()
            ;
            CurrencyRate::factory(2)
                ->create([
                    'currency' => $currencyName,
                    'created_at' => $this->carbon->subMonth()->subDays(2)->format('Y-m-d H:i:s'),
                ])
            ;

            $result = $this->currencyRateService->getCurrencyRatesOfLastMonth($currencyName);
            $mappedResult = array_map(fn ($item) => $item->id, $result);
            $this->assertSame($expected, $mappedResult);
        }

        // Check on empty after truncate
        CurrencyRate::query()->truncate();
        $currencyName = $currencies[0];
        $expected = [];

        $result = $this->currencyRateService->getCurrencyRatesOfLastMonth($currencyName);
        $this->assertSame($expected, $result);
    }
}
