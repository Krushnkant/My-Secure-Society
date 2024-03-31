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
        Schema::create('daily_post_report', function (Blueprint $table) {
            $table->bigIncrements('daily_post_report_id')->index();
            $table->integer('society_daily_post_id')->index();
            $table->integer('daily_post_report_option_id');
            $table->enum('daily_post_report_status', [1, 2])->default(1)->comment('1 - Post Hidden, 2 - No Action')->index();
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_post_report');
    }
};
