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
        Schema::create('amenity_booking', function (Blueprint $table) {
            $table->bigIncrements('amenity_booking_id')->index();
            $table->integer('amenity_slot_id')->index();
            $table->integer('booking_no')->index();
            $table->dateTime('booking_time');
            $table->integer('booking_status')->enum([1, 2, 3])->default(1)->comment('1 - Booked, 2 - Pending, 3 - Cancelled')->index();
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by')->index();
            $table->dateTime('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('updated_by')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amenity_booking');
    }
};