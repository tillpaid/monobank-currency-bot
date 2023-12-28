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

    public function getId(): int
    {
        return $this->id;
    }

    // TODO: Don't use it directly when you will use Money library.
    public function getCurrency(): string
    {
        return $this->currency;
    }

    // TODO: Don't use it directly when you will use Money library.
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getSell(): ?float
    {
        return $this->sell;
    }

    public function setSell(?float $sell): self
    {
        $this->sell = $sell;

        return $this;
    }

    public function getBuy(): ?float
    {
        return $this->buy;
    }

    public function setBuy(?float $buy): self
    {
        $this->buy = $buy;

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
