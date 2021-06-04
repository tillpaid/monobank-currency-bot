<?php

namespace Database\Factories;

use App\Models\TelegramUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class TelegramUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TelegramUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'chat_id'          => $this->faker->numberBetween(100000, 999999),
            'state'            => null,
            'state_additional' => null,
        ];
    }
}
