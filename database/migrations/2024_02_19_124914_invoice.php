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
        Schema::create('Invoice', function (Blueprint $table) {
            $table->bigIncrements('InvoiceId')->index();
            $table->integer('InvoiceType')->index()->comment('1 - Maintenance, 2 - Loan Interest, 3 - Loan Payment, 4 - Move In, 5 - Move Out, 6 - Amenity Booking, 7 - Payment Invoice');
            $table->integer('InvoiceToUserType')->index()->comment('1 - Society Member, 2 - Guard, 3 - Staff Member, 4 - Other');
            $table->integer('BlockFlatId')->index()->comment('0 if InvoiceToUserType is not 1(Society Member)');
            $table->integer('InvoiceToUserId');
            $table->string('InvoiceUserName', 50);
            $table->string('InvoiceNo', 30)->comment('Format: YYYY+SocietyId+0001');
            $table->dateTime('InvoicePeriodStartDate');
            $table->dateTime('InvoicePeriodEndDate');
            $table->date('DueDate');
            $table->decimal('InvoiceAmount', 10, 2);
            $table->integer('PenaltyStatus')->enum([1, 2])->default(1)->comment('1 - No Penalty, 2 - Penalty Applied')->index();
            $table->integer('eStatus')->enum([1, 3])->default(1)->comment('1 - Active, 3 - Delete')->index();
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
        Schema::dropIfExists('Invoice');
    }
};
