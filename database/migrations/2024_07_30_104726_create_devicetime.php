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
        Schema::create('DeviceTime', function (Blueprint $table) {
            $table->bigIncrements('DeviceTimeID');
            $table->unsignedBigInteger('DeviceID');
            $table->unsignedBigInteger('Time');
            $table->decimal('Rate', 8, 2);
            $table->unsignedBigInteger('TimeTypeID');
            $table->boolean('Active')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('DeviceTime');
    }
};
