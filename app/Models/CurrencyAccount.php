<?php

declare(strict_types=1);

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * // TODO: I think that we don't need to store a uah_value. We can calculate it using purchase_rate and currency_value.
 *
 * @property int           $id
 * @property int           $telegram_user_id
 * @property string        $currency
 * @property float         $uah_value
 * @property float         $purchase_rate
 * @property float         $currency_value
 * @property null|DateTime $created_at
 * @property null|DateTime $updated_at
 * @property TelegramUser  $telegramUser
 */
class CurrencyAccount extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'uah_value' => 'float',
        'purchase_rate' => 'float',
        'currency_value' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class);
    }

    // TODO: Should I delete it?
    public function getTelegramUser(): TelegramUser
    {
        return $this->telegramUser;
    }

    // TODO: Should I delete it?
    public function setTelegramUser(TelegramUser $telegramUser): self
    {
        $this->telegramUser()->associate($telegramUser);

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    // TODO: I think we don't need this. And even a property for it.
    public function getTelegramUserId(): int
    {
        return $this->telegram_user_id;
    }

    // TODO: I think that we need to set it using TelegramUser model relation.
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

    // TODO: Don't use it directly when you will use Money library.
    public function getUahValue(): float
    {
        return $this->uah_value;
    }

    // TODO: Don't use it directly when you will use Money library.
    public function setUahValue(float $uahValue): self
    {
        $this->uah_value = $uahValue;

        return $this;
    }

    public function getPurchaseRate(): float
    {
        return $this->purchase_rate;
    }

    public function setPurchaseRate(float $purchaseRate): self
    {
        $this->purchase_rate = $purchaseRate;

        return $this;
    }

    // TODO: Don't use it directly when you will use Money library.
    public function getCurrencyValue(): float
    {
        return $this->currency_value;
    }

    // TODO: Don't use it directly when you will use Money library.
    public function setCurrencyValue(float $currencyValue): self
    {
        $this->currency_value = $currencyValue;

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
