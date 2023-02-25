<?php

namespace App\Services\Interfaces\Models;

interface TelegramUserSendRateServiceInterface
{
    public function checkIfRateChangeBeenSent(int $telegramUserId, int $currencyRateId): bool;

    public function updateSendRate(int $telegramUserId, int $currencyRateId, string $currency): void;
}
