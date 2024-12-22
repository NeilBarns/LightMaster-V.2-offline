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
        Schema::create('RptDeviceTimeTransactions', function (Blueprint $table) {
            $table->bigIncrements('TransactionID');
            $table->unsignedBigInteger('DeviceTimeTransactionsID');
            $table->unsignedBigInteger('DeviceID');
            $table->string('TransactionType', 11);
            $table->boolean('IsOpenTime')->default(false)->nullable();
            $table->dateTime('Time');
            $table->enum('StoppageType', ['AUTO', 'MANUAL'])->nullable();
            $table->integer('Duration');
            $table->decimal('Rate', 8, 2);
            $table->string('Reason', 255)->nullable();
            $table->unsignedBigInteger('CreatedByUserId')->nullable();

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
        Schema::dropIfExists('RptDeviceTimeTransactions');
    }
};
