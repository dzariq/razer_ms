<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentResponseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_channel_response', function (Blueprint $table) {
            $table->increments('id');
            $table->string('transaction_reference');
            $table->string('skey',150);
            $table->string('tranID',150);
            $table->string('domain',50);
            $table->string('status',50);
            $table->string('amount',50);
            $table->string('currency',50);
            $table->string('paydate',50);
            $table->string('orderid',50);
            $table->string('appcode',50)->nullable();
            $table->string('error_code',150)->nullable();
            $table->string('error_desc',150);
            $table->string('channel',50);

            // Add other columns as needed
            $table->timestamps();
        });

        // Seed the table with initial data
       // $this->seedData();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_channel_response');
    }

}
