<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStateAdditionalFieldToTelegramUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telegram_users', function (Blueprint $table) {
            $table->json('state_additional')->nullable()->after('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telegram_users', function (Blueprint $table) {
            $table->dropColumn('state_additional');
        });
    }
}
