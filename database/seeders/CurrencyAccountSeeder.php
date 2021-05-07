<?php

namespace Database\Seeders;

use App\Models\CurrencyAccount;
use Illuminate\Database\Seeder;

class CurrencyAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CurrencyAccount::factory(1000)->create();
    }
}
