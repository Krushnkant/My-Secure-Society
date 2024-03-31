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
        Schema::create('guard_campus_visit_attendance', function (Blueprint $table) {
            $table->bigIncrements('guard_campus_visit_attendance_id');
            $table->integer('society_guard_id')->index();
            $table->integer('guard_Campus_visit_time_id')->index();
            $table->integer('society_guard_visit_area_id')->index();
            $table->integer('is_visited');
            $table->dateTime('attended_time')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guard_campus_visit_attendance');
    }
};
