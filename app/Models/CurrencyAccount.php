<?php

declare(strict_types=1);

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
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
}
