<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int                          $id
 * @property string                       $chat_id
 * @property null|string                  $state
 * @property null|array                   $state_additional
 * @property null|string                  $created_at
 * @property null|string                  $updated_at
 * @property Collection|CurrencyAccount[] $currencyAccounts
 */
class TelegramUser extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'state_additional' => 'array',
    ];

    public function currencyAccounts(): HasMany
    {
        return $this->hasMany(CurrencyAccount::class);
    }
}
