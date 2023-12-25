<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\TelegramUserSendRate;

class TelegramUserSendRateRepository
{
    private TelegramUserSendRate $telegramUserSendRate;

    public function __construct(TelegramUserSendRate $telegramUserSendRate)
    {
        $this->telegramUserSendRate = $telegramUserSendRate;
    }

    public function rowExists(int $telegramUserId, int $currencyRateId): bool
    {
        $count = $this->telegramUserSendRate
            ->newQuery()
            ->where('telegram_user_id', $telegramUserId)
            ->where('currency_rate_id', $currencyRateId)
            ->count()
        ;

        return $count > 0;
    }

    public function getSendRate(int $telegramUserId, string $currency): ?TelegramUserSendRate
    {
        return $this->telegramUserSendRate
            ->newQuery()
            ->where('telegram_user_id', $telegramUserId)
            ->where('currency', $currency)
            ->get()
            ->first()
        ;
    }
}
