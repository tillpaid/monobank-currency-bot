<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\TelegramUser;
use Tests\TestCase;

class CurrencyAccountTest extends TestCase
{
    public function testCurrencyAccountHasTelegramUser(): void
    {
        $telegramUser = $this->fixturesHelper->createTelegramUser();
        $currencyAccount = $this->fixturesHelper->createCurrencyAccount($telegramUser);

        $this->assertNotNull($currencyAccount->telegramUser);
        $this->assertInstanceOf(TelegramUser::class, $currencyAccount->telegramUser);
    }
}
