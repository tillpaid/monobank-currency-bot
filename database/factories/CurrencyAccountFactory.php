<?php

namespace Database\Factories;

use App\Models\CurrencyAccount;
use App\Models\CurrencyRate;
use App\Models\TelegramUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CurrencyAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $telegramUser = TelegramUser::inRandomOrder()->first();
        $currencyRate = CurrencyRate::inRandomOrder()->first();

        $uahValue = $this->faker->randomFloat(2, 10, 10000);
        $purchaseRate = $currencyRate->sell;
        $currencyValue = $uahValue / $purchaseRate;

        return [
            'telegram_user_id' => $telegramUser->id,
            'currency'         => $currencyRate->currency,
            'uah_value'        => $uahValue,
            'purchase_rate'    => $purchaseRate,
            'currency_value'   => $currencyValue,
        ];
    }
}
