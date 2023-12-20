<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $telegram_user_id
 * @property string $currency
 * @property float $uah_value
 * @property float $purchase_rate
 * @property float $currency_value
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property TelegramUser $telegramUser
 */
class CurrencyAccount extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class);
    }
}
