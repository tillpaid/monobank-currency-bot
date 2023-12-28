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

    public function testUpdateSendRateNotExists(): void
    {
        $telegramUser = $this->fixturesHelper->createTelegramUser();
        $currencyRate = $this->fixturesHelper->createCurrencyRate();
        $this->fixturesHelper->createTelegramUserSendRate($telegramUser->getId(), $currencyRate->getId());

        $this->telegramUserSendRateService->updateSendRate($telegramUser->getId(), $currencyRate->getId(), $currencyRate->getCurrency());

        $sendRates = TelegramUserSendRate::all();
        $this->assertCount(1, $sendRates);

        $sendRate = $sendRates->first();
        $this->assertSame($telegramUser->getId(), $sendRate->telegram_user_id);
        $this->assertSame($currencyRate->getId(), $sendRate->currency_rate_id);
        $this->assertSame($currencyRate->getCurrency(), $sendRate->currency);
    }

    public function testUpdateSendRateExists(): void
    {
        $telegramUser = $this->fixturesHelper->createTelegramUser();
        $currencyRate = $this->fixturesHelper->createCurrencyRate();

        $this->telegramUserSendRateService->updateSendRate($telegramUser->getId(), $currencyRate->getId(), $currencyRate->getCurrency());

        $sendRates = TelegramUserSendRate::all();
        $this->assertCount(1, $sendRates);

        $sendRate = $sendRates->first();
        $this->assertSame($telegramUser->getId(), $sendRate->telegram_user_id);
        $this->assertSame($currencyRate->getId(), $sendRate->currency_rate_id);
        $this->assertSame($currencyRate->getCurrency(), $sendRate->currency);
    }
}
