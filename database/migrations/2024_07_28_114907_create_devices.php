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
        Schema::create('Devices', function (Blueprint $table) {
            $table->bigIncrements('DeviceID');
            $table->string('DeviceName', 50);
            $table->string('ExternalDeviceName', 50);
            $table->string('Description')->nullable();
            $table->unsignedBigInteger('DeviceStatusID');
            $table->boolean('IsOnline')->nullable();
            $table->string('ClientName', 50)->nullable();
            $table->string('IPAddress', 20)->nullable();
            $table->unsignedBigInteger('RemainingTimeNotification')->nullable();
            $table->unsignedBigInteger('WatchdogInterval')->nullable();
            $table->dateTime('OperationDate')->nullable();
            $table->dateTime('last_heartbeat')->nullable();
            $table->timestamps();
            
            //Indexes
            $table->index('DeviceName');
            $table->index('ExternalDeviceName');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Devices');
    }
};
