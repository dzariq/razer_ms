<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantPaymentChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_payment_channels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('channel_name');
            // Add other columns as needed
            $table->timestamps();

        });

        $this->seedData();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_payment_channels');
    }

      /**
     * Seed the table with initial data.
     *
     * @return void
     */
    private function seedData()
    {
        // You can seed the table with initial error codes and descriptions here
        DB::table('merchant_payment_channels')->insert([
            ['channel_name' => 'UNIONPAY'],
            ['channel_name' => 'WECHATPAY'],
            ['channel_name' => 'ALIPAY'],
            // Add more seed data as needed
        ]);
    }
}
