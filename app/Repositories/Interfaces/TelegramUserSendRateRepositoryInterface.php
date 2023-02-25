<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface TelegramUserSendRateRepositoryInterface
{
    public function rowExists(int $telegramUserId, int $currencyRateId): bool;

    public function getSendRate(int $telegramUserId, string $currency): ?Model;
}
