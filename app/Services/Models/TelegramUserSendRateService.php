<?php

namespace App\Services\Models;

use App\Models\TelegramUserSendRate;
use App\Repositories\Interfaces\TelegramUserSendRateRepositoryInterface;
use App\Services\Interfaces\Models\TelegramUserSendRateServiceInterface;

class TelegramUserSendRateService implements TelegramUserSendRateServiceInterface
{
    private TelegramUserSendRateRepositoryInterface $telegramUserSendRateRepository;

    public function __construct(TelegramUserSendRateRepositoryInterface $telegramUserSendRateRepository)
    {
        $this->telegramUserSendRateRepository = $telegramUserSendRateRepository;
    }

    public function checkIfRateChangeBeenSent(int $telegramUserId, int $currencyRateId): bool
    {
        return $this->telegramUserSendRateRepository->rowExists($telegramUserId, $currencyRateId);
    }

    public function updateSendRate(int $telegramUserId, int $currencyRateId, string $currency): void
    {
        if ($sendRate = $this->telegramUserSendRateRepository->getSendRate($telegramUserId, $currency)) {
            $sendRate->currency_rate_id = $currencyRateId;
            $sendRate->save();
        } else {
            TelegramUserSendRate::create([
                'telegram_user_id' => $telegramUserId,
                'currency'         => $currency,
                'currency_rate_id' => $currencyRateId,
            ]);
        }
    }
}
