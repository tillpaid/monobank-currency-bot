<?php

namespace App\Repositories;

use App\Models\TelegramUserSendRate;
use App\Repositories\Interfaces\TelegramUserSendRateRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TelegramUserSendRateRepository
 * @package App\Repositories
 */
class TelegramUserSendRateRepository implements TelegramUserSendRateRepositoryInterface
{
    /**
     * @var TelegramUserSendRate
     */
    private $model;

    /**
     * TelegramUserSendRateRepository constructor.
     * @param TelegramUserSendRate $telegramUserSendRate
     */
    public function __construct(TelegramUserSendRate $telegramUserSendRate)
    {
        $this->model = $telegramUserSendRate;
    }

    /**
     * @inheritDoc
     */
    public function rowExists(int $telegramUserId, int $currencyRateId): bool
    {
        $count = $this->model
            ->where('telegram_user_id', $telegramUserId)
            ->where('currency_rate_id', $currencyRateId)
            ->count();

        return $count > 0;
    }

    /**
     * @inheritDoc
     */
    public function getSendRate(int $telegramUserId, string $currency): ?Model
    {
        return $this->model
            ->where('telegram_user_id', $telegramUserId)
            ->where('currency', $currency)
            ->first();
    }
}
