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
        Schema::create('AmenityBooking', function (Blueprint $table) {
            $table->bigIncrements('AmenityBookingId')->index();
            $table->integer('AmenitySlotId')->index();
            $table->integer('BookingNo')->index();
            $table->dateTime('BookingTime');
            $table->integer('BookingStatus')->enum([1, 2, 3])->default(1)->comment('1 - Booked, 2 - Pending, 3 - Cancelled')->index();
            $table->dateTime('CreatedAt')->nullable();
            $table->integer('CreatedBy')->index();
            $table->dateTime('UpdatedAt')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('UpdatedBy')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('AmenityBooking');
    }
};
