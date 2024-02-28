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
        Schema::create('InvoiceItem', function (Blueprint $table) {
            $table->bigIncrements('InvoiceItemId')->index();
            $table->integer('InvoiceId')->index();
            $table->integer('InvoiceItemType')->index()->comment('1 - Maintenance[SocietyChargeId], 2 - Loan Interest[LoanRequestId] , 3 - Loan Payment[LoanRequestId], 4 - Move In[SocietyChargeId], 5 - Move Out[SocietyChargeId], 6 - Amenity Booking[AmenityBookingId], 7 - Payment Invoice[SocietyChargeId], 8 - Penalty [SocietyChargeId]');
            $table->integer('InvoiceItemMasterId')->index()->comment('SocietyChargeId, LoanRequestId, AmenityBookingId');
            $table->string('InvoiceItemDescription');
            $table->decimal('InvoiceItemAmount', 10, 2);
            $table->integer('eStatus')->enum([1, 3])->default(1)->comment('1 - Active, 3 - Delete')->index();
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
        Schema::dropIfExists('InvoiceItem');
    }
};
