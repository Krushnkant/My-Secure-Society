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
        Schema::create('User', function (Blueprint $table) {
            $table->bigIncrements('UserId')->index();
            $table->integer('UserType')->enum([1, 2, 3, 4])->default(4)->comment('1 - Company Admin User, 2 - Resident App User, 3 - Guard App User, 4 - App User, 5 - Daily Help User, 6 - Staff Member');
            $table->integer('UserCode')->unique();
            $table->string('FullName', 70);
            $table->string('MobileNo', 15);
            $table->string('Email', 50)->unique();
            // $table->dateTime('EmailVerifiedAt')->nullable();
            $table->string('Password', 255)->nullable();
            $table->string('ProfilePicUrl')->nullable();
            $table->integer('Gender')->enum([1, 2])->comment('1 - Female, 2 - Male')->index();
            $table->string('BloodGroup')->enum(['A+, A-, B+, B-, O+, O-, AB+, AB-'])->nullable()->index();
            $table->string('FirebaseId')->nullable();
            $table->string('Token')->nullable();
            $table->integer('eStatus')->enum([1, 2, 3])->default(1)->comment('1 - Active, 2 - InActive, 3 - Delete')->index();
            $table->dateTime('CreatedAt')->nullable();
            $table->integer('CreatedBy')->index();
            $table->dateTime('UpdatedAt')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('UpdatedBy')->index();
            $table->softDeletes('DeletedAt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('User');
    }
};
