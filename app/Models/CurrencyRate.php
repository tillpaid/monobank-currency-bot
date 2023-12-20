<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $currency
 * @property float|null $sell
 * @property float|null $buy
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class CurrencyRate extends Model
{
    use HasFactory;

    protected $guarded = [];
}
