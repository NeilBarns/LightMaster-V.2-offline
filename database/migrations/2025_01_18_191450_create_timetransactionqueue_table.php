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
        Schema::create('TimeTransactionQueue', function (Blueprint $table) {
            $table->bigIncrements('TimeTransactionQueueID');
            $table->unsignedBigInteger('DeviceID');
            $table->unsignedBigInteger('DeviceStatusID');
            $table->unsignedBigInteger('Thread');
            $table->dateTime('EndTime');
            $table->string('StoppageType', 10)->nullable();
            $table->unsignedBigInteger('QueueStatusID');
            $table->string('ErrorMessage', 500)->nullable();
            $table->timestamps();

            // Indexes
            $table->index('DeviceID');
            $table->index('Thread');
            $table->index('EndTime');
            $table->index('QueueStatusID');
            $table->index('ErrorMessage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('TimeTransactionQueue');
    }
};
