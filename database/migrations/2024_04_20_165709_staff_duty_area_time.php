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
        Schema::create('staff_duty_area_time', function (Blueprint $table) {
            $table->bigIncrements('duty_area_time_id')->index();
            $table->integer('society_staff_member_id')->index();
            $table->integer('staff_duty_area_id')->index();
            $table->integer('is_standing_location')->comment('1 - True, 2 - False')->index();
            $table->time('visit_time');
            $table->integer('eStatus')->enum([1, 2, 3])->default(1)->comment('1 - Active, 2 - Inactive, 3 - Delete')->index();
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
        Schema::dropIfExists('staff_duty_area_time');
    }
};
