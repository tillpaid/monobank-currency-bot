<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Monobank;

use App\Models\CurrencyRate;
use App\Services\Monobank\MonobankCurrencyService;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Container\BindingResolutionException;
use Mockery\MockInterface;
use Tests\TestCase;

class MonobankCurrencyServiceTest extends TestCase
{
    private Client|MockInterface $client;
    private MonobankCurrencyService $monobankCurrencyService;

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->mock(Client::class);
        $this->monobankCurrencyService = $this->app->make(MonobankCurrencyService::class);
    }

    /**
     * @dataProvider provideUpdateCurrencyRatesCases
     *
     * @param array<array<string, float|string>> $expectedRates
     * @param array<array<string, float|string>> $existingRates
     *
     * @throws Exception
     */
    public function testUpdateCurrencyRates(bool $expectedOutput, array $expectedRates, array $existingRates): void
    {
        foreach ($existingRates as $existingRate) {
            $this->fixturesHelper->createCurrencyRate(
                $existingRate['currency'],
                $existingRate['rateSell'],
                $existingRate['rateBuy']
            );
        }

        $clientResponse = [
            ['currencyCodeA' => 840, 'currencyCodeB' => 980, 'rateSell' => 27.12, 'rateBuy' => 27.23],
            ['currencyCodeA' => 978, 'currencyCodeB' => 980, 'rateSell' => 41.23, 'rateBuy' => 42.34],
            ['currencyCodeA' => 999, 'currencyCodeB' => 980, 'rateSell' => 10.45, 'rateBuy' => 11.56],
            ['currencyCodeA' => 840, 'currencyCodeB' => 978, 'rateSell' => 10.78, 'rateBuy' => 10.89],
        ];

        $this->client
            ->shouldReceive('get')
            ->once()
            ->andReturn(new Response(200, [], json_encode($clientResponse)))
        ;

        $result = $this->monobankCurrencyService->updateCurrencyRates();
        $this->assertSame($expectedOutput, $result);

        $currencyRates = CurrencyRate::all();
        $this->assertCount(count($expectedRates), $currencyRates);

        $rates = array_map(
            static fn (CurrencyRate $currencyRate) => [
                'currency' => $currencyRate->getCurrency(),
                'rateSell' => $currencyRate->getSell(),
                'rateBuy' => $currencyRate->getBuy(),
            ],
            $currencyRates->all()
        );

        $this->assertSame($expectedRates, $rates);
    }

    /**
     * @return array<array<string, mixed>>
     */
    public static function provideUpdateCurrencyRatesCases(): iterable
    {
        return [
            'Rates not exist' => [
                'expectedOutput' => true,
                'expectedRates' => [
                    ['currency' => 'usd', 'rateSell' => 27.12, 'rateBuy' => 27.23],
                    ['currency' => 'eur', 'rateSell' => 41.23, 'rateBuy' => 42.34],
                ],
                'existingRates' => [],
            ],
            'Rates exist and not changed' => [
                'expectedOutput' => false,
                'expectedRates' => [
                    ['currency' => 'usd', 'rateSell' => 27.12, 'rateBuy' => 27.23],
                    ['currency' => 'eur', 'rateSell' => 41.23, 'rateBuy' => 42.34],
                ],
                'existingRates' => [
                    ['currency' => 'usd', 'rateSell' => 27.12, 'rateBuy' => 27.23],
                    ['currency' => 'eur', 'rateSell' => 41.23, 'rateBuy' => 42.34],
                ],
            ],
            'Rates exist and changed' => [
                'expectedOutput' => true,
                'expectedRates' => [
                    ['currency' => 'usd', 'rateSell' => 26.00, 'rateBuy' => 27.00],
                    ['currency' => 'eur', 'rateSell' => 40.00, 'rateBuy' => 41.00],
                    ['currency' => 'usd', 'rateSell' => 27.12, 'rateBuy' => 27.23],
                    ['currency' => 'eur', 'rateSell' => 41.23, 'rateBuy' => 42.34],
                ],
                'existingRates' => [
                    ['currency' => 'usd', 'rateSell' => 26.00, 'rateBuy' => 27.00],
                    ['currency' => 'eur', 'rateSell' => 40.00, 'rateBuy' => 41.00],
                ],
            ],
            'Eur changed and usd not changed' => [
                'expectedOutput' => true,
                'expectedRates' => [
                    ['currency' => 'usd', 'rateSell' => 27.12, 'rateBuy' => 27.23],
                    ['currency' => 'eur', 'rateSell' => 40.00, 'rateBuy' => 41.00],
                    ['currency' => 'eur', 'rateSell' => 41.23, 'rateBuy' => 42.34],
                ],
                'existingRates' => [
                    ['currency' => 'usd', 'rateSell' => 27.12, 'rateBuy' => 27.23],
                    ['currency' => 'eur', 'rateSell' => 40.00, 'rateBuy' => 41.00],
                ],
            ],
            'Usd changed and eur not changed' => [
                'expectedOutput' => true,
                'expectedRates' => [
                    ['currency' => 'usd', 'rateSell' => 26.00, 'rateBuy' => 27.00],
                    ['currency' => 'eur', 'rateSell' => 41.23, 'rateBuy' => 42.34],
                    ['currency' => 'usd', 'rateSell' => 27.12, 'rateBuy' => 27.23],
                ],
                'existingRates' => [
                    ['currency' => 'usd', 'rateSell' => 26.00, 'rateBuy' => 27.00],
                    ['currency' => 'eur', 'rateSell' => 41.23, 'rateBuy' => 42.34],
                ],
            ],
        ];
    }

    public function testUpdateCurrencyRatesException(): void
    {
        $this->client
            ->shouldReceive('get')
            ->once()
            ->andThrow(new Exception())
        ;

        $result = $this->monobankCurrencyService->updateCurrencyRates();
        $this->assertFalse($result);

        $currencyRates = CurrencyRate::all();
        $this->assertCount(0, $currencyRates);
    }

    public function testUpdateCurrencyRatesBadResponseCode(): void
    {
        $this->client
            ->shouldReceive('get')
            ->once()
            ->andReturn(new Response(500))
        ;

        $result = $this->monobankCurrencyService->updateCurrencyRates();
        $this->assertFalse($result);

        $currencyRates = CurrencyRate::all();
        $this->assertCount(0, $currencyRates);
    }
}
