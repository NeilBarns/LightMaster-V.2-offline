<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Exchange', function (Blueprint $table) {
            $table->bigIncrements('ExchangeID');
            $table->unsignedBigInteger('DeviceID');
            $table->integer('Thread')->default(0);
            $table->boolean('Active')->default(false);
            $table->string('SerialNumber', 50)->nullable();
            $table->string('ExchangeSerialNumber', 50)->nullable();
            $table->string('Reason', 255)->nullable();
            $table->unsignedBigInteger('ExchangeStatusID');
            $table->timestamps();
            
            //Indexes
            $table->index('ExchangeID');
            $table->index('DeviceID');
            $table->index('SerialNumber');
            $table->index('ExchangeSerialNumber');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Exchange');
    }
};
