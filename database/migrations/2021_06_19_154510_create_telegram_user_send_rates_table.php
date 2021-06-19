<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateTelegramUserSendRatesTable
 */
class CreateTelegramUserSendRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_user_send_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('telegram_user_id');
            $table->string('currency');
            $table->unsignedBigInteger('currency_rate_id');
            $table->timestamps();

            $table->foreign('telegram_user_id')
                ->references('id')
                ->on('telegram_users')
                ->onDelete('cascade');

            $table->foreign('currency_rate_id')
                ->references('id')
                ->on('currency_rates')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telegram_user_send_rates');
    }
}
