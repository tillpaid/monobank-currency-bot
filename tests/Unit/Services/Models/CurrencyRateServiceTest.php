<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Models;

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
        $this->assertSame($currency, $currencyRate->getCurrency());
        $this->assertSame($sell, $currencyRate->getSell());
        $this->assertSame($buy, $currencyRate->getBuy());
    }
}
