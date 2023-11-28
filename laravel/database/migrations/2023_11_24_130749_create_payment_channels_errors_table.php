<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentChannelsErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_channels_errors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('error_code')->unique();
            $table->text('description');
            // Add other columns as needed
            $table->timestamps();
        });

        // Seed the table with initial data
        $this->seedData();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_channels_errors');
    }

    /**
     * Seed the table with initial data.
     *
     * @return void
     */
    private function seedData()
    {
        // You can seed the table with initial error codes and descriptions here
        DB::table('payment_channels_errors')->insert([
            ['error_code' => '22', 'description' => 'Error 001 Description'],
            // Add more seed data as needed
        ]);
    }
}
