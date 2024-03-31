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
        Schema::create('user_device_info', function (Blueprint $table) {
            $table->bigIncrements('user_device_info_id')->index();
            $table->integer('user_id')->index();
            $table->string('device_id');
            $table->string('imei_no');
            $table->string('phone_company');
            $table->string('device_modal');
            $table->string('device_os');
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
        Schema::dropIfExists('user_device_info');
    }
};
