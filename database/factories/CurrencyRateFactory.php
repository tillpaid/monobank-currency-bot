<?php

namespace Database\Factories;

use App\Models\CurrencyRate;
use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyRateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CurrencyRate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $sell = $this->faker->randomFloat(2, 15, 40);
        $buy = $this->faker->randomFloat(2, $sell, $sell + 1);

        return [
            'currency' => $this->faker->randomElement(config('monobank.currencies')),
            'sell'     => $sell,
            'buy'      => $buy,
        ];
    }
}
