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
        Schema::create('invoice', function (Blueprint $table) {
            $table->bigIncrements('invoice_id')->index();
            $table->integer('invoice_type')->index()->comment('1 - Maintenance, 2 - Loan Interest, 3 - Loan Payment, 4 - Move In, 5 - Move Out, 6 - Amenity Booking, 7 - Payment Invoice');
            $table->integer('invoice_to_user_type')->index()->comment('1 - Society Member, 2 - Guard, 3 - Staff Member, 4 - Other');
            $table->integer('block_flat_id')->index()->comment('0 if invoice_to_user_type is not 1(Society Member)');
            $table->integer('invoice_to_user_id');
            $table->string('invoice_user_name', 50);
            $table->string('invoice_no', 30)->comment('Format: YYYY+SocietyId+0001');
            $table->dateTime('invoice_period_start_date');
            $table->dateTime('invoice_period_end_date');
            $table->date('due_date');
            $table->decimal('invoice_amount', 10, 2);
            $table->integer('penalty_status')->enum([1, 2])->default(1)->comment('1 - No Penalty, 2 - Penalty Applied')->index();
            $table->integer('estatus')->enum([1, 3])->default(1)->comment('1 - Active, 3 - Delete')->index();
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
        Schema::dropIfExists('invoice');
    }
};
