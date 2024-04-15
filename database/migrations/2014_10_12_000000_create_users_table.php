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
        Schema::create('user', function (Blueprint $table) {
            $table->bigIncrements('user_id')->index();
            $table->integer('user_type')->enum([1, 2, 3, 4])->default(4)->comment('1 - Company Admin User, 2 - Resident App User, 3 - Guard App User, 4 - App User, 5 - Daily Help User, 6 - Staff Member');
            $table->integer('user_code')->unique();
            $table->string('full_name', 70)->nullable();
            $table->string('mobile_no', 15);
            $table->string('email', 50)->nullable();
            // $table->dateTime('EmailVerifiedAt')->nullable();
            $table->string('password', 255)->nullable();
            $table->string('profile_pic_url', 1000)->nullable();
            $table->string('cover_photo_url', 1000)->nullable();
            $table->integer('gender')->default(1)->enum([1, 2])->comment('1 - Female, 2 - Male')->index();
            $table->string('blood_group')->enum(['A+, A-, B+, B-, O+, O-, AB+, AB-'])->nullable()->index();
            $table->string('firebase_id')->nullable();
            $table->string('token')->nullable();
            $table->integer('estatus')->enum([1, 2, 3])->default(1)->comment('1 - Active, 2 - InActive, 3 - Delete')->index();
            $table->integer('created_by')->index();
            $table->integer('updated_by')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
