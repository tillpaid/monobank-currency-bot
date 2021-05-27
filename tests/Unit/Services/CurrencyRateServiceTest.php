<?php

namespace Tests\Unit\Services;

use App\Models\CurrencyRate;
use App\Services\Interfaces\Models\CurrencyRateServiceInterface;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;

/**
 * Class CurrencyRateServiceTest
 * @package Tests\Unit\Services
 */
class CurrencyRateServiceTest extends TestCase
{
    /**
     * @var CurrencyRateServiceInterface
     */
    private $currencyRateService;

    /**
     * @return void
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->currencyRateService = Container::getInstance()->make(CurrencyRateServiceInterface::class);
    }

    /**
     * @test
     * @return void
     */
    public function testCreateCurrencyRate(): void
    {
        $currencies = config('monobank.currencies');
        $currencyName = array_shift($currencies);
        $sell = $this->faker->randomFloat(5, 10, 100);
        $buy = $this->faker->randomFloat(5, 10, 100);

        $result = $this->currencyRateService->createCurrencyRate($currencyName, $sell, $buy);
        $this->assertTrue($result);
    }

    /**
     * @test
     * @return void
     */
    public function testGetLatestCurrencyRate(): void
    {
        $currencies = config('monobank.currencies');

        // Check on null
        CurrencyRate::truncate();
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
                    ->first();
                $result = $this->currencyRateService->getLatestCurrencyRate($currencyName);

                $this->assertEquals($expected->toArray(), $result->toArray());
            }
        }
    }

    /**
     * @test
     * @return void
     */
    public function testGetLastTwoCurrencyRates(): void
    {
        $currencies = config('monobank.currencies');
        $currencyName = $currencies[0];

        // Check on null after truncate
        CurrencyRate::truncate();
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
                    ->create(['currency' => $currencyName]);
                $expected = array_reverse($expected->toArray());
                $result = $this->currencyRateService->getLastTwoCurrencyRates($currencyName);

                $this->assertEquals($expected, $result->toArray());
            }
        }
    }

    /**
     * @test
     * @return void
     */
    public function testGetCurrencyRatesOfLastMonth(): void
    {
        $currencies = config('monobank.currencies');
        CurrencyRate::truncate();

        // Check when result is Collection
        foreach ($currencies as $currencyName) {
            $expected = CurrencyRate::factory(5)
                ->create(['currency' => $currencyName])
                ->toArray();
            CurrencyRate::factory(2)
                ->create([
                    'currency'   => $currencyName,
                    'created_at' => $this->carbon->subMonth()->subDays(2)->format('Y-m-d H:i:s')
                ]);

            $result = $this->currencyRateService->getCurrencyRatesOfLastMonth($currencyName);
            $this->assertEquals($expected, $result->toArray());
        }

        // Check on empty after truncate
        CurrencyRate::truncate();
        $currencyName = $currencies[0];
        $expected = [];

        $result= $this->currencyRateService->getCurrencyRatesOfLastMonth($currencyName);
        $this->assertEquals($expected, $result->toArray());
    }
}
