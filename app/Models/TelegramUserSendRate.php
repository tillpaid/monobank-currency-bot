<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int         $id
 * @property int         $telegram_user_id
 * @property string      $currency
 * @property int         $currency_rate_id
 * @property null|string $created_at
 * @property null|string $updated_at
 */
class TelegramUserSendRate extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'telegram_user_id' => 'integer',
        'currency_rate_id' => 'integer',
    ];
}
