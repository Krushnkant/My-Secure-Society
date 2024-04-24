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
        Schema::create('generated_otp', function (Blueprint $table) {
            $table->bigIncrements('generated_otp_id')->index();
            $table->string('mobile_no', 15);
            $table->integer('otp_code');
            $table->dateTime('expire_time')->comment('GenerateTime + 30 Min');
            $table->dateTime('created_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_otp');
    }
};
