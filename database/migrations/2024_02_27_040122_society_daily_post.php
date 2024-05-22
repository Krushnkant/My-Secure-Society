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
        Schema::create('society_daily_post', function (Blueprint $table) {
            $table->bigIncrements('society_daily_post_id')->index();
            $table->unsignedBigInteger('society_id')->index();
            $table->unsignedBigInteger('parent_post_id')->index()->comment('0 for Main Post');
            $table->enum('post_type', [1, 2, 3, 4])->default(1)->comment('1 - Discussion Question, 2 - Poll, 3 - Event, 4 - Replay')->index();
            $table->string('post_description', 500);
            $table->string('bg_color', 20)->nullable();
            $table->integer('total_like')->index();
            $table->integer('total_comment')->index();
            $table->integer('total_shared')->index();
            $table->dateTime('event_time')->nullable();
            $table->string('event_venue', 150)->nullable();
            // $table->enum('is_reported', [1, 2])->default(2)->comment('1 - True, 2 - False')->index();
            $table->integer('daily_post_report_option_id')->nullable();
            $table->enum('estatus', [1, 2, 3, 4, 5])->default(1)->comment('1 - Active, 2 - InActive, 3 - Delete, 4 - Pending, 5 - Reported')->index();
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
        Schema::dropIfExists('SocietyDailyPost');
    }
};
