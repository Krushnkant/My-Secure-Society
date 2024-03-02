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
            $table->integer('user_id')->index();
            $table->string('DeviceId');
            $table->string('ImeiNo');
            $table->string('PhoneCompany');
            $table->string('DeviceModal');
            $table->string('DeviceOs');
            $table->dateTime('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('updated_by')->index();
            $table->softDeletes('deleted_at');
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
