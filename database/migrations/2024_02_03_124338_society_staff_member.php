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
        Schema::create('society_staff_member', function (Blueprint $table) {
            $table->bigIncrements('society_staff_member_id')->index();
            $table->integer('user_id')->index();
            $table->integer('society_department_id')->index();
            $table->integer('resident_designation_id')->index();
            $table->string('duty_note', 255)->nullable();
            $table->time('duty_start_time')->nullable();
            $table->time('duty_end_time')->nullable();
            $table->string('weekly_off_days')->nullable()->comment('1 - Mon, 2 - Tue, 3 - Wed, 4 - Thu, 5 - Fri, 6 - Sat, 7 - Sun');
            $table->integer('estatus')->enum([1, 2, 3, 4])->default(1)->comment('1 - Active, 2 - InActive, 3 - Delete, 4 - Pending')->index();
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
        Schema::dropIfExists('society_staff_member');
    }
};
