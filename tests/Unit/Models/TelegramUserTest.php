<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\CurrencyAccount;
use Tests\TestCase;

class TelegramUserTest extends TestCase
{
    public function testCurrencyAccountHasTelegramUser(): void
    {
        $telegramUser = $this->fixturesHelper->createTelegramUser();
        $this->fixturesHelper->createCurrencyAccount($telegramUser);
        $this->fixturesHelper->createCurrencyAccount($telegramUser);
        $this->fixturesHelper->createCurrencyAccount($telegramUser);

        $this->assertCount(3, $telegramUser->currencyAccounts);

        foreach ($telegramUser->currencyAccounts as $currencyAccount) {
            $this->assertInstanceOf(CurrencyAccount::class, $currencyAccount);
        }
    }
}
