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
            $table->integer('society_id')->index();
            $table->integer('block_flat_id')->index();
            $table->integer('ServiceVendorId')->index();
            $table->integer('CollectionOTP');
            $table->integer('CourierCollectionStatus')->comment('1 - Pending, 2 - Delivered')->index();
            $table->integer('CollectedByUserId')->nullable()->index();
            $table->dateTime('CollectedTime')->nullable();
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
        Schema::dropIfExists('DeliveredCourierAtGate');
    }
};
