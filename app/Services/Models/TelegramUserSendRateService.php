<?php

declare(strict_types=1);

namespace App\Services\Models;

use App\Models\TelegramUserSendRate;
use App\Repositories\TelegramUserSendRateRepository;

class TelegramUserSendRateService
{
    public function __construct(
        private TelegramUserSendRateRepository $telegramUserSendRateRepository,
    ) {
    }

    // TODO: This is not only about update. Needs to be renamed or refactored.
    public function updateSendRate(int $telegramUserId, int $currencyRateId, string $currency): void
    {
        if ($sendRate = $this->telegramUserSendRateRepository->getSendRate($telegramUserId, $currency)) {
            $sendRate->setCurrencyRateId($currencyRateId);
            $sendRate->save();
        } else {
            $telegramUserSendRate = new TelegramUserSendRate();
            $telegramUserSendRate->setTelegramUserId($telegramUserId);
            $telegramUserSendRate->setCurrency($currency);
            $telegramUserSendRate->setCurrencyRateId($currencyRateId);
            $telegramUserSendRate->save();
        }
    }
}
