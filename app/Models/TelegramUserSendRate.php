<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $telegram_user_id
 * @property string $currency
 * @property int $currency_rate_id
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class TelegramUserSendRate extends Model
{
    use HasFactory;

    protected $guarded = [];
}
