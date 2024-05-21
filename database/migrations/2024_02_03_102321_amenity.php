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
        Schema::create('amenity', function (Blueprint $table) {
            $table->bigIncrements('amenity_id')->index();
            $table->integer('society_id')->index();
            $table->string('amenity_name', 100);
            $table->string('amenity_description', 500)->nullable();
            $table->integer('is_chargeable')->enum([1, 2])->default(1)->comment('1 - True, 2 - false')->index();
            $table->integer('applicable_payment_type')->enum([1, 2, 3])->default(1)->comment('1 - Offline, 2 - Online, 3 - Other')->index();
            $table->integer('booking_type')->comment('1 - Single Booking in Single Slot, 2 - Multiple Booking in Single Slot')->index();
            $table->integer('max_capacity')->index();
            // $table->integer('max_booking_per_slot');
            // $table->integer('max_people_per_booking')->comment('0 - Unlimited If max_booking_per_slot is 1');
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
        Schema::dropIfExists('amenity');
    }
};
