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
        Schema::create('ActivityLog', function (Blueprint $table) {
            $table->bigIncrements('LogID');
            $table->string('Entity');
            $table->unsignedBigInteger('EntityID')->nullable();
            $table->text('Log');
            $table->string('Type');
            $table->unsignedBigInteger('CreatedByUserId')->nullable();
            $table->timestamps();

            //Indexes
            $table->index('Entity');
            $table->index('EntityID');
            $table->index('CreatedByUserId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ActivityLog');
    }
};
