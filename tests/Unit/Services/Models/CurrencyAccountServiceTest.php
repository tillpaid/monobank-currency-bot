<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Models;

use App\Repositories\CurrencyAccountRepository;
use App\Services\Models\CurrencyAccountService;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;

class CurrencyAccountServiceTest extends TestCase
{
    private CurrencyAccountRepository $currencyAccountRepository;
    private CurrencyAccountService $currencyAccountService;

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->currencyAccountRepository = $this->app->make(CurrencyAccountRepository::class);
        $this->currencyAccountService = $this->app->make(CurrencyAccountService::class);
    }

    public function testCreate(): void
    {
        $currency = 'EUR';
        $uahValue = 10_000.0;
        $purchaseRate = 41.0;
        $currencyValue = round($uahValue / $purchaseRate, 5);

        $telegramUser = $this->fixturesHelper->createTelegramUser();
        $result = $this->currencyAccountService->create($telegramUser->getId(), $currency, $uahValue, $purchaseRate);
        $this->assertTrue($result);

        $currencyAccount = $this->currencyAccountRepository->getFirstUserCurrencyAccount($telegramUser->getId(), $currency);
        $this->assertSame($telegramUser->getId(), $currencyAccount->getTelegramUser()->getId());
        $this->assertSame($currency, $currencyAccount->getCurrency());
        $this->assertSame($uahValue, $currencyAccount->getUahValue());
        $this->assertSame($purchaseRate, $currencyAccount->getPurchaseRate());
        $this->assertSame($currencyValue, $currencyAccount->getCurrencyValue());
    }

    /**
     * @throws Exception
     */
    public function testSellCurrency(): void
    {
        $currency = 'EUR';

        $this->fixturesHelper->createCurrencyRate($currency);
        $telegramUser = $this->fixturesHelper->createTelegramUser();

        $uahValues = [
            100 => 0.85,
            1_000 => 0.75,
            10_000 => 0.65,
            100_000 => 0.55,
            1_000_000 => 0.45,
        ];

        foreach ($uahValues as $uahValue => $purchaseRate) {
            $this->currencyAccountService->create($telegramUser->getId(), $currency, $uahValue, $purchaseRate);
        }

        $currentSum = $this->currencyAccountRepository->getUserCurrencySum($telegramUser->getId(), $currency);
        $expectedAmountAfterSell = round($currentSum * 0.85, 5);
        $amountToSell = round($currentSum * 0.15, 5);

        $this->currencyAccountService->sellCurrency($telegramUser->getId(), $currency, $amountToSell);
        $amountAfterSell = $this->currencyAccountRepository->getUserCurrencySum($telegramUser->getId(), $currency);

        // TODO: Change it when migrate to Money library
        $this->assertSame(round($expectedAmountAfterSell, 5), round($amountAfterSell, 5));
    }

    public function testSellCurrencyNothingToSell(): void
    {
        $telegramUser = $this->fixturesHelper->createTelegramUser();

        $this->currencyAccountService->sellCurrency($telegramUser->getId(), 'EUR', 100);
        $this->assertSame(0, 0);
    }
}
