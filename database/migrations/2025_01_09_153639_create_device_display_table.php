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
        Schema::create('DeviceDisplay', function (Blueprint $table) {
            $table->unsignedBigInteger('DeviceID');
            $table->string('TransactionType', 11);
            $table->boolean('IsOpenTime')->default(false)->nullable();
            $table->dateTime('StartTime');
            $table->dateTime('EndTime')->nullable();
            $table->dateTime('PauseTime')->nullable();
            $table->dateTime('ResumeTime')->nullable();
            $table->integer('TotalTime')->default(0);
            $table->decimal('TotalRate', 8, 2)->default(0);
            $table->timestamps();

            // Indexes
            $table->index('DeviceID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('DeviceDisplay');
    }
};
