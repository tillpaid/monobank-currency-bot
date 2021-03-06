<?php

namespace Tests\Unit\Services;

use App\Models\CurrencyAccount;
use App\Models\TelegramUser;
use App\Services\Interfaces\Models\CurrencyAccountServiceInterface;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;

/**
 * Class CurrencyAccountServiceTest
 * @package Tests\Unit\Services
 */
class CurrencyAccountServiceTest extends TestCase
{
    /**
     * @var CurrencyAccountServiceInterface
     */
    private $currencyAccountService;

    /**
     * @return void
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->currencyAccountService = Container::getInstance()->make(CurrencyAccountServiceInterface::class);
    }

    /**
     * @test
     * @return void
     */
    public function testCreate(): void
    {
        $telegramUser = TelegramUser::factory(1)->create()->first();
        $currencies = config('monobank.currencies');
        $currencyName = array_shift($currencies);

        $result = $this->currencyAccountService->create($telegramUser->id, $currencyName, 5, 1);
        $this->assertTrue($result);
    }

    /**
     * @test
     * @return void
     */
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
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @test
     * @return void
     */
    public function testGetFirstUserCurrencyAccount(): void
    {
        $telegramUser = TelegramUser::factory(1)->hasCurrencyAccounts(20)->create()->first();
        $currencies = config('monobank.currencies');

        foreach ($currencies as $currencyName) {
            $expected = CurrencyAccount
                ::where('telegram_user_id', $telegramUser->id)
                ->where('currency', $currencyName)
                ->first();
            $result = $this->currencyAccountService->getFirstUserCurrencyAccount($telegramUser->id, $currencyName);

            $this->assertEquals($expected, $result);
        }
    }

    /**
     * @test
     * @return void
     */
    public function testSellCurrency(): void
    {
        $telegramUser = TelegramUser::factory(1)->hasCurrencyAccounts(20)->create()->first();
        $currencies = config('monobank.currencies');

        foreach ($currencies as $currencyName) {
            $currencySum = $this->currencyAccountService->getUserCurrencySum($telegramUser->id, $currencyName);
            $expected = $currencySum * 0.85;
            $toSell = $currencySum * 0.15;

            $this->currencyAccountService->sellCurrency($telegramUser->id, $currencyName, $toSell);
            $currencySumAfterSell = $this->currencyAccountService->getUserCurrencySum($telegramUser->id, $currencyName);

            $this->assertEquals($expected, $currencySumAfterSell);
        }
    }

    /**
     * @test
     * @return void
     */
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

        $this->assertEquals($expected, $result);
    }
}
