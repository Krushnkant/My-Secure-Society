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
        Schema::create('daily_help_provider_work_flat', function (Blueprint $table) {
            $table->bigIncrements('work_flat_id')->index();
            $table->integer('daily_help_provider_id')->index();
            $table->integer('block_flat_id')->index();
            $table->time('work_start_time');
            $table->time('work_end_time');
            $table->integer('contract_status')->enum([1, 2, 3, 4])->default(1)->comment('1 - Running, 2 - Ended')->index();
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by')->index();
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
        Schema::dropIfExists('daily_help_provider_work_flat');
    }
};
