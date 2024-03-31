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
        Schema::create('invoice_item', function (Blueprint $table) {
            $table->bigIncrements('invoice_item_id')->index();
            $table->integer('invoice_id')->index();
            $table->integer('invoice_item_type')->index()->comment('1 - Maintenance[society_charge_id], 2 - Loan Interest[LoanRequestId] , 3 - Loan Payment[LoanRequestId], 4 - Move In[society_charge_id], 5 - Move Out[society_charge_id], 6 - Amenity Booking[AmenityBookingId], 7 - Payment Invoice[society_charge_id], 8 - Penalty [society_charge_id]');
            $table->integer('invoice_item_master_id')->index()->comment('society_charge_id, LoanRequestId, AmenityBookingId');
            $table->string('invoice_item_description');
            $table->decimal('invoice_item_amount', 10, 2);
            $table->integer('estatus')->enum([1, 3])->default(1)->comment('1 - Active, 3 - Delete')->index();
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
        Schema::dropIfExists('invoice_item');
    }
};
