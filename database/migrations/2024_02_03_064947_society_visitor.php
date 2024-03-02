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
        Schema::create('SocietyVisitor', function (Blueprint $table) {
            $table->bigIncrements('SocietyVisitorId')->index();
            $table->integer('society_id')->index();
            $table->integer('block_flat_id')->comment('0 for Campus Visitor')->index();
            $table->integer('VisitorType')->enum([1, 2, 3, 4, 5])->default(1)->comment('1 - Delivery, 2 - Cab, 3 - Guest, 4 - Daily Help, 5 - Campus Visitor')->index();
            $table->integer('ServiceVendorId')->comment('0 for Non-Service Visit (Guest, Daily Help, Other option of Delivery, and Cab which haven not existed)')->index();
            $table->integer('DailyHelpProviderId')->comment('0 If Visitor Gatepass is not for DailyHelp Service Provider')->index();
            $table->integer('VisitorUserId')->comment('0 for Non App User')->index();
            $table->string('CompanyName', 100)->nullable();
            $table->string('VisitorName', 100)->nullable();
            $table->string('VisitorMobileNo', 15)->nullable();
            $table->integer('TotalVisitors');
            $table->string('VehicleNumber', 30)->nullable();
            $table->dateTime('EntryTime');
            // $table->dateTime('ExitTime');
            $table->integer('VisitorStatus')->enum([1, 2, 3, 4, 5])->default(1)->comment('1 - Pre Approved, 2 - Approved 3 - Rejected, 4 - Pending, 5 - Cancelled')->index();
            $table->integer('ApprovedBy')->comment('0 till not Approved');
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
        Schema::dropIfExists('SocietyVisitor');
    }
};
