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
        Schema::create('SocietyDailyPost', function (Blueprint $table) {
            $table->bigIncrements('SocietyDailyPostId')->index();
            $table->integer('society_id')->index();
            $table->integer('ParentPostId')->index()->comment('0 for Main Post');
            $table->integer('PostType')->enum([1, 2, 3])->default(1)->comment('1 - Discussion Question, 2 - Poll, 3 - Event, 4 - Replay')->index();
            $table->string('PostDescription', 500);
            $table->string('BgColor', 20)->nullable();
            $table->integer('TotalLike')->index();
            $table->integer('TotalComment')->index();
            $table->integer('TotalShared')->index();
            $table->dateTime('EventTime')->nullable();
            $table->string('EventVenue', 150)->nullable();
            $table->integer('IsReported')->enum([1, 2])->default(2)->comment('1 - True, 2 - False')->index();
            $table->integer('eStatus')->enum([1, 2, 3, 4])->default(1)->comment('1 - Active, 2 - InActive, 3 - Delete, 4 - Pending')->index();
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
