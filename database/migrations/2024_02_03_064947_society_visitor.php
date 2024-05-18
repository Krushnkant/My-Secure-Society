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
        Schema::create('society_visitor', function (Blueprint $table) {
            $table->bigIncrements('society_visitor_id')->index();
            $table->integer('society_id')->index();
            $table->integer('block_flat_id')->comment('0 for Campus Visitor')->index();
            $table->integer('visitor_type')->enum([1, 2, 3, 4, 5])->default(1)->comment('1 - Delivery, 2 - Cab, 3 - Guest, 4 - Daily Help, 5 - Campus Visitor')->index();
            $table->integer('service_vendor_id')->comment('0 for Non-Service Visit (Guest, Daily Help, Other option of Delivery, and Cab which haven not existed)')->index();
            $table->integer('daily_help_provider_id')->comment('0 If Visitor Gatepass is not for DailyHelp Service Provider')->index();
            $table->integer('visitor_user_id')->comment('0 for Non App User')->index();
            $table->string('company_name', 100)->nullable();
            $table->string('visitor_name', 100)->nullable();
            $table->string('visitor_mobile_no', 15)->nullable();
            $table->integer('total_visitors');
            $table->string('vehicle_number', 30)->nullable();
            $table->dateTime('entry_time');
            // $table->dateTime('exit_time');
            $table->integer('visitor_status')->enum([1, 2, 3, 4, 5])->default(4)->comment('1 - Pre Approved, 2 - Approved 3 - Rejected, 4 - Pending, 5 - Cancelled')->index();
            $table->integer('approved_by')->comment('0 till not Approved');
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
        Schema::dropIfExists('society_visitor');
    }
};
