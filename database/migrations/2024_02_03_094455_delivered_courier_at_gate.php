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
        Schema::create('DeliveredCourierAtGate', function (Blueprint $table) {
            $table->bigIncrements('DeliveredCourierAtGateId')->index();
            $table->integer('SocietyId')->index();
            $table->integer('BlockFlatId')->index();
            $table->integer('ServiceVendorId')->index();
            $table->integer('CollectionOTP');
            $table->integer('CourierCollectionStatus')->comment('1 - Pending, 2 - Delivered')->index();
            $table->integer('CollectedByUserId')->nullable()->index();
            $table->dateTime('CollectedTime')->nullable();
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
        Schema::dropIfExists('DeliveredCourierAtGate');
    }
};
