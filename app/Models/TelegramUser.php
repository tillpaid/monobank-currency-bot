<?php

declare(strict_types=1);

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int                          $id
 * @property string                       $chat_id
 * @property null|string                  $state
 * @property null|array                   $state_additional
 * @property null|DateTime                $created_at
 * @property null|DateTime                $updated_at
 * @property Collection|CurrencyAccount[] $currencyAccounts
 */
class TelegramUser extends Model
{
    use HasFactory;

    public const STATE_DEFAULT = null;
    public const STATE_BUY = 'buy';
    public const STATE_BUY_SUM = 'buy-sum';
    public const STATE_BUY_RATE = 'buy-rate';
    public const STATE_BUY_RATE_OWN = 'buy-rate-own';
    public const STATE_SELL = 'sell';
    public const STATE_SELL_SUM = 'sell-sum';
    public const STATE_SELL_CONFIRM = 'sell-confirm';
    public const STATE_STATISTICS_CURRENCY = 'statistics-currency';

    public const STATE_ADDITIONAL_BUY_CURRENCY = 'buy-currency';
    public const STATE_ADDITIONAL_BUY_CURRENCY_SUM = 'buy-currency-sum';
    public const STATE_ADDITIONAL_BUY_CURRENCY_RATE = 'buy-currency-rate';
    public const STATE_ADDITIONAL_SELL_CURRENCY = 'sell-currency';
    public const STATE_ADDITIONAL_SELL_CURRENCY_SUM = 'sell-currency-sum';
    public const STATE_ADDITIONAL_SELL_CURRENCY_SUM_ALL = 'sell-currency-sum-all';

    protected $guarded = [];

    protected $casts = [
        'state_additional' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function currencyAccounts(): HasMany
    {
        return $this->hasMany(CurrencyAccount::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getChatId(): string
    {
        return $this->chat_id;
    }

    public function setChatId(string $chatId): self
    {
        $this->chat_id = $chatId;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    // TODO: Test is this is possible to actually have null here and save it to DB
    // TODO: Rename to getAdditionalState
    public function getStateAdditional(): ?array
    {
        return $this->state_additional;
    }

    public function getStateAdditionalValue(string $key): ?string
    {
        return $this->state_additional[$key] ?? null;
    }

    // TODO: Temp solution, need to update code and don't use this method
    public function getStateAdditionalFloatValue(string $key): ?float
    {
        $value = $this->state_additional[$key] ?? null;

        return $value ? (float) $value : null;
    }

    public function setStateAdditional(?array $stateAdditional): self
    {
        $this->state_additional = $stateAdditional;

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
