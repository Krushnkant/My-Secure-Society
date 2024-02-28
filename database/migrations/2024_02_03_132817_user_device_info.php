<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('UserDeviceInfo', function (Blueprint $table) {
            $table->bigIncrements('UserDeviceInfoId')->index();
            $table->integer('UserId')->index();
            $table->string('DeviceId');
            $table->string('ImeiNo');
            $table->string('PhoneCompany');
            $table->string('DeviceModal');
            $table->string('DeviceOs');
            $table->dateTime('UpdatedAt')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('UpdatedBy')->index();
            $table->softDeletes('DeletedAt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('UserDeviceInfo');
    }
};
