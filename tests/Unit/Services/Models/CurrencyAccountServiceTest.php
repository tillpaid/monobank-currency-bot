<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Models;

use App\Models\CurrencyAccount;
use App\Models\CurrencyRate;
use App\Models\TelegramUser;
use App\Repositories\CurrencyAccountRepository;
use App\Services\Models\CurrencyAccountService;
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
        $result = $this->currencyAccountService->create($telegramUser->id, $currency, $uahValue, $purchaseRate);
        $this->assertTrue($result);

        $currencyAccount = $this->currencyAccountRepository->getFirstUserCurrencyAccount($telegramUser->id, $currency);
        $this->assertSame($telegramUser->id, $currencyAccount->telegramUser->id);
        $this->assertSame($currency, $currencyAccount->currency);
        $this->assertSame($uahValue, $currencyAccount->uah_value);
        $this->assertSame($purchaseRate, $currencyAccount->purchase_rate);
        $this->assertSame($currencyValue, $currencyAccount->currency_value);
    }

    public function testGetUserCurrencySum(): void
    {
        $telegramUser = TelegramUser::factory(1)->create()->first();
        $currencies = config('monobank.currencies');

        $expectedResults = [];

        foreach ($currencies as $currencyName) {
            $expectedResults[$currencyName] = $expectedResults[$currencyName] ?? 0;
            $counter = $this->faker->numberBetween(10, 50);

            while ($counter-- > 0) {
                $uahValue = $this->faker->randomFloat(2, 100, 100000);
                $purchaseRate = $this->faker->randomFloat(5, 10, 100);

                $expectedResults[$currencyName] += round($uahValue / $purchaseRate, 5);
                $this->currencyAccountService->create($telegramUser->id, $currencyName, $uahValue, $purchaseRate);
            }
        }

        foreach ($expectedResults as $currencyName => $expected) {
            $result = $this->currencyAccountService->getUserCurrencySum($telegramUser->id, $currencyName);
            $this->assertSame($expected, $result);
        }
    }

    public function testGetFirstUserCurrencyAccount(): void
    {
        CurrencyRate::factory(1)->create();
        $telegramUser = TelegramUser::factory(1)->hasCurrencyAccounts(20)->create()->first();
        $currencies = config('monobank.currencies');

        foreach ($currencies as $currencyName) {
            $expected = CurrencyAccount
                ::where('telegram_user_id', $telegramUser->id)
                    ->where('currency', $currencyName)
                    ->first()
            ;
            $result = $this->currencyAccountService->getFirstUserCurrencyAccount($telegramUser->id, $currencyName);

            if (null === $expected) {
                $this->assertNull($result);
            } else {
                $this->assertSame($expected->id, $result->id);
            }
        }
    }

    public function testSellCurrency(): void
    {
        $currency = 'EUR';

        $this->fixturesHelper->createCurrencyRate($currency);
        $telegramUser = $this->fixturesHelper->createTelegramUser();

        for ($i = 0; $i < 10; ++$i) {
            $uahValue = $this->faker->randomFloat(2, 100, 100_000);
            $purchaseRate = $this->faker->randomFloat(5, 10, 100);

            $this->currencyAccountService->create($telegramUser->id, $currency, $uahValue, $purchaseRate);
        }

        $currentSum = $this->currencyAccountService->getUserCurrencySum($telegramUser->id, $currency);
        $expectedAmountAfterSell = round($currentSum * 0.85, 5);
        $amountToSell = round($currentSum * 0.15, 5);

        $this->currencyAccountService->sellCurrency($telegramUser->id, $currency, $amountToSell);
        $amountAfterSell = $this->currencyAccountService->getUserCurrencySum($telegramUser->id, $currency);

        $this->assertSame($expectedAmountAfterSell, $amountAfterSell);
    }

    public function testSellCurrencyNothingToSell(): void
    {
        $telegramUser = $this->fixturesHelper->createTelegramUser();

        $this->currencyAccountService->sellCurrency($telegramUser->id, 'EUR', 100);
        $this->assertSame(0, 0);
    }

    public function testGetUserBalanceSum(): void
    {
        $telegramUser = TelegramUser::factory(1)->create()->first();
        $currencies = config('monobank.currencies');

        $expected = [];

        foreach ($currencies as $currencyName) {
            $expected[$currencyName] = $expected[$currencyName] ?? ['currency_value' => 0, 'uah_value' => 0];
            $counter = $this->faker->numberBetween(10, 50);

            while ($counter-- > 0) {
                $uahValue = $this->faker->randomFloat(2, 100, 100000);
                $purchaseRate = $this->faker->randomFloat(5, 10, 100);

                $expected[$currencyName]['currency_value'] += round($uahValue / $purchaseRate, 5);
                $expected[$currencyName]['uah_value'] += $uahValue;

                $this->currencyAccountService->create($telegramUser->id, $currencyName, $uahValue, $purchaseRate);
            }

            $expected[$currencyName]['currency_value'] = round($expected[$currencyName]['currency_value'], 5);
            $expected[$currencyName]['uah_value'] = round($expected[$currencyName]['uah_value'], 5);
        }

        $result = $this->currencyAccountService->getUserBalanceSum($telegramUser->id);

        asort($expected);
        asort($result);

        $this->assertSame($expected, $result);
    }
}
