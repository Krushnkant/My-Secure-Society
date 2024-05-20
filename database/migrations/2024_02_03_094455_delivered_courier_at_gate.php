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
        Schema::create('delivered_courier_at_gate', function (Blueprint $table) {
            $table->bigIncrements('delivered_courier_at_gate_id')->index();
            $table->integer('society_id')->index();
            $table->integer('block_flat_id')->index();
            $table->integer('service_vendor_id')->index();
            $table->string('company_name', 100)->nullable();
            $table->integer('total_parcel');
            $table->string('courier_note', 100)->nullable();
            $table->integer('collection_otp');
            $table->integer('courier_collection_status')->comment('1 - Pending, 2 - Delivered')->index();
            // $table->integer('collected_by_user_id')->nullable()->index();
            $table->dateTime('collected_time')->nullable();
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
        Schema::dropIfExists('delivered_courier_at_gate');
    }
};
