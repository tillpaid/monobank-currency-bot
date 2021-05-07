<?php

namespace Database\Seeders;

use App\Models\TelegramUser;
use Illuminate\Database\Seeder;

class TelegramUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TelegramUser::factory(10)->create();
    }
}
