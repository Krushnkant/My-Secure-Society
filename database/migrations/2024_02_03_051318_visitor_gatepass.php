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
        Schema::create('VisitorGatepass', function (Blueprint $table) {
            $table->bigIncrements('VisitorGatepassId')->index();
            $table->integer('SocietyId')->index();
            $table->integer('BlockFlatId')->index();
            $table->integer('VisitorType')->enum([1, 2, 3, 4])->default(1)->comment('1 - Delivery, 2 - Cab, 3 - Guest, 4 - Daily Help')->index();
            $table->integer('ServiceVendorId')->comment('0 for Non-Service Visit (Guest, Daily Help, Other option of Delivery, and Cab which haven not existed)')->index();
            $table->integer('DailyHelpProviderId')->comment('0 If Visitor Gatepass is not for DailyHelp Service Provider')->index();
            $table->integer('VisitorUserId')->comment('0 for Non App User')->index();
            $table->string('CompanyName', 100)->nullable();
            $table->string('VisitorName', 100);
            $table->string('VisitorMobileNo', 15);
            $table->string('InvitationMessage', 300);
            $table->integer('TotalVisitors');
            $table->date('ValidFromDate')->comment('ValidFromDate and ValidToDate both will be same If Gatepass for a Single Entry');
            $table->date('ValidToDate');
            $table->string('AllowedDays')->enum(['Mon, Tue, Wed, Thu, Fri, Sat, Sun'])->comment('Mon, Tue, Wed, Thu, Fri, Sat, Sun');
            $table->time('ValidFromTime');
            $table->time('ValidToTime');
            $table->integer('GatepassStatus')->enum([1, 2, 3, 4])->default(1)->comment('1 - Pre Approved, 2 - Rejected 3 - Cancelled, 4 - Pending')->index();
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
        Schema::dropIfExists('VisitorGatepass');
    }
};
