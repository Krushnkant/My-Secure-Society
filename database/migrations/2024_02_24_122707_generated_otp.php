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
        Schema::create('GeneratedOtp', function (Blueprint $table) {
            $table->bigIncrements('GeneratedOtpId')->index();
            $table->string('MobileNo', 15);
            $table->integer('OtpCode');
            $table->dateTime('ExpireTime')->comment('GenerateTime + 30 Min');
            $table->dateTime('created_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('GeneratedOtp');
    }
};
