<?php

declare(strict_types=1);

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int           $id
 * @property string        $currency
 * @property null|float    $sell
 * @property null|float    $buy
 * @property null|DateTime $created_at
 * @property null|DateTime $updated_at
 */
class CurrencyRate extends Model
{
    use HasFactory;

    protected $guarded = [];

    // TODO: Change type of sell and buy to float in database
    protected $casts = [
        'sell' => 'float',
        'buy' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
