<?php

namespace Database\Seeders;

use App\Models\CurrencyRate;
use App\Models\TelegramUser;
use App\Models\TelegramUserSendRate;
use Illuminate\Database\Seeder;

class TelegramUserSendRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $telegramUsers = TelegramUser::all();
        $currencies = config('monobank.currencies');

        foreach ($telegramUsers as $user) {
            foreach ($currencies as $currency) {
                $currencyRate = CurrencyRate
                    ::where('currency', $currency)
                    ->inRandomOrder()
                    ->first();

                TelegramUserSendRate::create([
                    'telegram_user_id' => $user->getId(),
                    'currency'         => $currency,
                    'currency_rate_id' => $currencyRate->id,
                ]);
            }
        }
    }
}
