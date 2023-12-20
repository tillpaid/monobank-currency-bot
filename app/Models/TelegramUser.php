<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $chat_id
 * @property string|null $state
 * @property array|null $state_additional
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property CurrencyAccount[]|Collection $currencyAccounts
 */
class TelegramUser extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'state_additional' => 'array'
    ];

    public function currencyAccounts(): HasMany
    {
        return $this->hasMany(CurrencyAccount::class);
    }
}
