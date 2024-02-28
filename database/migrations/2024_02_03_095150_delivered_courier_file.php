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
        Schema::create('DeliveredCourierFile', function (Blueprint $table) {
            $table->bigIncrements('DeliveredCourierFileId')->index();
            $table->integer('DeliveredCourierAtGateId')->index();
            $table->integer('FileType')->enum([1])->default(1)->comment('1 - Image')->index();
            $table->string('FileUrl', 500);
            $table->dateTime('UploadedAt')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('DeliveredCourierFile');
    }
};
