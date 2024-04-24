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
        Schema::create('staff_duty_attendance', function (Blueprint $table) {
            $table->bigIncrements('staff_duty_attendance_id')->index();
            $table->integer('society_staff_member_id')->index();
            $table->integer('duty_area_time_id')->index();
            $table->string('selfie_photo', 500);
            $table->integer('attendance_status')->enum([1, 2, 3])->default(1)->comment('1 - Present, 2 - Absent, 3 - Pending')->index();
            $table->dateTime('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('updated_by')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_duty_attendance');
    }
};
