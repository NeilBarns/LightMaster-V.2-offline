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
        Schema::create('Notifications', function (Blueprint $table) {
            $table->bigIncrements('NotificationID');
            $table->string('Notification', 500);
            $table->unsignedBigInteger('NotificationLevelID');
            $table->unsignedBigInteger('NotificationSourceID');
            $table->unsignedBigInteger('DeviceID');
            $table->timestamps();
            
            //Indexes
            $table->index('NotificationID');
            $table->index('Notification');
            $table->index('NotificationLevelID');
            $table->index('NotificationSourceID');
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
        Schema::dropIfExists('Notifications');
    }
};
