<?php

declare(strict_types=1);

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * // TODO: Why there is no relation to TelegramUser? Add back relation from TelegramUser to TelegramUserSendRate.
 * // TODO: Why there is no relation to CurrencyRate? Add back relation from CurrencyRate to TelegramUserSendRate.
 *
 * @property int           $id
 * @property int           $telegram_user_id
 * @property string        $currency
 * @property int           $currency_rate_id
 * @property null|DateTime $created_at
 * @property null|DateTime $updated_at
 */
class TelegramUserSendRate extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'telegram_user_id' => 'integer',
        'currency_rate_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getTelegramUserId(): int
    {
        return $this->telegram_user_id;
    }

    public function setTelegramUserId(int $telegramUserId): self
    {
        $this->telegram_user_id = $telegramUserId;

        return $this;
    }

    // TODO: Don't use it directly when you will use Money library.
    public function getCurrency(): string
    {
        return $this->currency;
    }

    // TODO: Don't use it directly when you will use Money library.
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCurrencyRateId(): int
    {
        return $this->currency_rate_id;
    }

    public function setCurrencyRateId(int $currencyRateId): self
    {
        $this->currency_rate_id = $currencyRateId;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updated_at;
    }
}
