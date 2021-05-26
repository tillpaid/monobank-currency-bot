<?php

namespace Tests\Unit\Services;

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
}
