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
        Schema::create('DeviceTimeTransactions', function (Blueprint $table) {
            $table->bigIncrements('TransactionID');
            $table->unsignedBigInteger('DeviceID');
            $table->string('TransactionType', 11);
            $table->boolean('IsOpenTime')->default(false)->nullable();
            $table->dateTime('StartTime');
            $table->dateTime('EndTime')->nullable();
            $table->enum('StoppageType', ['AUTO', 'MANUAL'])->nullable();
            $table->integer('Duration');
            $table->decimal('Rate', 8, 2);
            $table->boolean('Active')->default(false);
            $table->string('Reason', 255)->nullable();
            $table->unsignedBigInteger('CreatedByUserId')->nullable();
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
        Schema::dropIfExists('DeviceTimeTransactions');
    }
};
