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
        Schema::create('DailyPostReport', function (Blueprint $table) {
            $table->bigIncrements('DailyPostReportId')->index();
            $table->integer('SocietyDailyPostId')->index();
            $table->integer('DailyPostReportOptionId');
            $table->integer('DailyPostReportStatus')->enum([1, 2])->default(1)->comment('1 - Post Hidden, 2 - No Action')->index();
            $table->dateTime('CreatedAt')->nullable();
            $table->integer('CreatedBy')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('DailyPostReport');
    }
};
