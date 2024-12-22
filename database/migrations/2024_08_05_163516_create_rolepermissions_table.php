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
        Schema::create('RolePermissions', function (Blueprint $table) {
            $table->bigIncrements('RolePermissionsID');
            $table->unsignedBigInteger('RoleID');
            $table->unsignedBigInteger('PermissionID');
            $table->string('CreatedByUserID')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('RolePermissions');
    }
};
