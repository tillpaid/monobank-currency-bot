<?php

declare(strict_types=1);

namespace App\Services\Models;

use App\Models\TelegramUserSendRate;
use App\Repositories\TelegramUserSendRateRepository;

readonly class TelegramUserSendRateService
{
    public function __construct(
        private TelegramUserSendRateRepository $telegramUserSendRateRepository,
    ) {}

    public function create(int $telegramUserId, int $currencyRateId, string $currency): bool
    {
        $telegramUserSendRate = new TelegramUserSendRate();
        $telegramUserSendRate->setTelegramUserId($telegramUserId);
        $telegramUserSendRate->setCurrency($currency);
        $telegramUserSendRate->setCurrencyRateId($currencyRateId);

        return $telegramUserSendRate->save();
    }

    public function upsert(int $telegramUserId, int $currencyRateId, string $currency): bool
    {
        $sendRate = $this->telegramUserSendRateRepository->getSendRate($telegramUserId, $currency);

        if ($sendRate === null) {
            return $this->create($telegramUserId, $currencyRateId, $currency);
        }

        $sendRate->setCurrencyRateId($currencyRateId);

        return $sendRate->save();
    }
}
