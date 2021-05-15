<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TelegramUser extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'state_additional' => 'array'
    ];

    /**
     * @return HasMany
     */
    public function currencyAccounts(): HasMany
    {
        return $this->hasMany(CurrencyAccount::class);
    }
}
