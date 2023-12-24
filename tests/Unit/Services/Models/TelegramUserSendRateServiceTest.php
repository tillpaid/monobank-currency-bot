<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Models;

use App\Models\TelegramUserSendRate;
use App\Services\Models\TelegramUserSendRateService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;

class TelegramUserSendRateServiceTest extends TestCase
{
    private TelegramUserSendRateService $telegramUserSendRateService;

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->telegramUserSendRateService = $this->app->make(TelegramUserSendRateService::class);
    }

    /** @dataProvider checkIfRateChangeBeenSentDataProvider */
    public function testCheckIfRateChangeBeenSent(bool $exists): void
    {
        if ($exists) {
            $telegramUser = $this->fixturesHelper->createTelegramUser();
            $currencyRate = $this->fixturesHelper->createCurrencyRate();
            $this->fixturesHelper->createTelegramUserSendRate($telegramUser->id, $currencyRate->id);
        }

        $result = $this->telegramUserSendRateService->checkIfRateChangeBeenSent(1, 1);
        $this->assertSame($exists, $result);
    }

    public function checkIfRateChangeBeenSentDataProvider(): array
    {
        return [
            'Exists' => ['exists' => true],
            'Not exists' => ['exists' => false],
        ];
    }

    public function testUpdateSendRateNotExists(): void
    {
        $telegramUser = $this->fixturesHelper->createTelegramUser();
        $currencyRate = $this->fixturesHelper->createCurrencyRate();
        $this->fixturesHelper->createTelegramUserSendRate($telegramUser->id, $currencyRate->id);

        $this->telegramUserSendRateService->updateSendRate($telegramUser->id, $currencyRate->id, $currencyRate->currency);

        $sendRates = TelegramUserSendRate::all();
        $this->assertCount(1, $sendRates);

        $sendRate = $sendRates->first();
        $this->assertSame($telegramUser->id, $sendRate->telegram_user_id);
        $this->assertSame($currencyRate->id, $sendRate->currency_rate_id);
        $this->assertSame($currencyRate->currency, $sendRate->currency);
    }

    public function testUpdateSendRateExists(): void
    {
        $telegramUser = $this->fixturesHelper->createTelegramUser();
        $currencyRate = $this->fixturesHelper->createCurrencyRate();

        $this->telegramUserSendRateService->updateSendRate($telegramUser->id, $currencyRate->id, $currencyRate->currency);

        $sendRates = TelegramUserSendRate::all();
        $this->assertCount(1, $sendRates);

        $sendRate = $sendRates->first();
        $this->assertSame($telegramUser->id, $sendRate->telegram_user_id);
        $this->assertSame($currencyRate->id, $sendRate->currency_rate_id);
        $this->assertSame($currencyRate->currency, $sendRate->currency);
    }
}
