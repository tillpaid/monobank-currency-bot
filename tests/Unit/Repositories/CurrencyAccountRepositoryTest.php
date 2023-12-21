<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Repositories\CurrencyAccountRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;

class CurrencyAccountRepositoryTest extends TestCase
{
    private CurrencyAccountRepository $currencyAccountRepository;

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->currencyAccountRepository = $this->app->make(CurrencyAccountRepository::class);
    }

    public function testGetUserCurrencySum(): void
    {
        $currencyAccounts = [
            'USD' => [100.12, 200.23, 300.34, 400.45, 500.56, 600.67, 700.78, 800.89, 900.90],
            'EUR' => [1000.123, 2000.234, 3000.345, 4000.456, 5000.567, 6000.678, 7000.789, 8000.890, 9000.901],
        ];
        $expectedSums = [
            'USD' => array_sum($currencyAccounts['USD']),
            'EUR' => array_sum($currencyAccounts['EUR']),
        ];

        $telegramUser = $this->fixturesHelper->createTelegramUser();
        foreach ($currencyAccounts as $currency => $currencyValues) {
            foreach ($currencyValues as $currencyValue) {
                $this->fixturesHelper->createCurrencyAccount($telegramUser, $currency, $currencyValue);
            }
        }

        foreach ($expectedSums as $currency => $sum) {
            $result = $this->currencyAccountRepository->getUserCurrencySum($telegramUser->id, $currency);
            $this->assertEquals($sum, $result);
        }
    }

    public function testGetFirstUserCurrencyAccount(): void
    {
        $currencyUsd = 'USD';
        $currencyEur = 'EUR';

        $telegramUser = $this->fixturesHelper->createTelegramUser();
        $firstUsdAccount = $this->fixturesHelper->createCurrencyAccount($telegramUser, $currencyUsd);
        $firstEurAccount = $this->fixturesHelper->createCurrencyAccount($telegramUser, $currencyEur);
        $this->fixturesHelper->createCurrencyAccount($telegramUser, $currencyUsd);
        $this->fixturesHelper->createCurrencyAccount($telegramUser, $currencyEur);

        $resultUsd = $this->currencyAccountRepository->getFirstUserCurrencyAccount($telegramUser->id, $currencyUsd);
        $this->assertEquals($firstUsdAccount->id, $resultUsd->id);

        $resultEur = $this->currencyAccountRepository->getFirstUserCurrencyAccount($telegramUser->id, $currencyEur);
        $this->assertEquals($firstEurAccount->id, $resultEur->id);
    }

    public function testGetLessProfitUserCurrencyAccount(): void
    {
        $telegramUser = $this->fixturesHelper->createTelegramUser();

        $this->fixturesHelper->createCurrencyAccount($telegramUser, 'EUR', 100, 100);
        $second = $this->fixturesHelper->createCurrencyAccount($telegramUser, 'EUR', 200, 200);

        $result = $this->currencyAccountRepository->getLessProfitUserCurrencyAccount($telegramUser->id, 'EUR');
        $this->assertEquals($second->id, $result->id);

        $result = $this->currencyAccountRepository->getLessProfitUserCurrencyAccount($telegramUser->id, 'USD');
        $this->assertNull($result);
    }

    public function testGetUserBalanceSum(): void
    {
        $rates = ['USD' => 37, 'EUR' => 41];
        $currencyAccounts = [
            'USD' => [100.12, 200.23, 300.34, 400.45, 500.56, 600.67, 700.78, 800.89, 900.90],
            'EUR' => [1000.123, 2000.234, 3000.345, 4000.456, 5000.567, 6000.678, 7000.789, 8000.890, 9000.901],
        ];

        $expectedSums = [];
        foreach ($currencyAccounts as $currency => $currencyValues) {
            $expectedSums[$currency] = [
                'currency_value' => (string)array_sum($currencyValues),
                'uah_value'      => (string)array_sum(array_map(
                    fn($value) => $value * $rates[$currency],
                    $currencyValues
                )),
            ];
        }

        $telegramUser = $this->fixturesHelper->createTelegramUser();
        foreach ($currencyAccounts as $currency => $currencyValues) {
            foreach ($currencyValues as $currencyValue) {
                $this->fixturesHelper->createCurrencyAccount($telegramUser, $currency, $currencyValue, $rates[$currency]);
            }
        }

        $result = $this->currencyAccountRepository->getUserBalanceSum($telegramUser->id);
        $this->assertEquals($expectedSums, $result);
    }
}