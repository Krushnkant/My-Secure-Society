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
        Schema::create('visitor_gatepass', function (Blueprint $table) {
            $table->bigIncrements('visitor_gatepass_id')->index();
            $table->integer('society_id')->index();
            $table->integer('block_flat_id')->index();
            $table->integer('visitor_type')->enum([1, 2, 3, 4])->default(1)->comment('1 - Delivery, 2 - Cab, 3 - Guest, 4 - Daily Help')->index();
            $table->integer('service_vendor_id')->comment('0 for Non-Service Visit (Guest, Daily Help, Other option of Delivery, and Cab which haven not existed)')->index();
            $table->integer('daily_help_provider_id')->comment('0 If Visitor Gatepass is not for DailyHelp Service Provider')->index();
            // $table->integer('visitor_user_id')->comment('0 for Non App User')->index();
            $table->string('company_name', 100)->nullable();
            $table->string('visitor_name', 100)->nullable();
            $table->string('visitor_mobile_no', 15)->nullable();
            $table->string('invitation_message', 300)->nullable();
            $table->string('vehicle_number', 10)->nullable();
            $table->integer('total_visitors');
            $table->date('valid_from_date')->comment('ValidFromDate and ValidToDate both will be same If Gatepass for a Single Entry');
            $table->date('valid_to_date');
            $table->string('allowed_days')->enum(['Mon, Tue, Wed, Thu, Fri, Sat, Sun'])->comment('Mon, Tue, Wed, Thu, Fri, Sat, Sun');
            $table->time('valid_from_time');
            $table->time('valid_to_time');
            $table->integer('visit_code')->unique();
            $table->integer('gatepass_status')->enum([1, 2, 3, 4])->default(1)->comment('1 - Pre Approved, 2 - Rejected 3 - Cancelled, 4 - Pending')->index();
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
        Schema::dropIfExists('visitor_gatepass');
    }
};
