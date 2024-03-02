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
        Schema::create('loan_request', function (Blueprint $table) {
            $table->bigIncrements('loan_request_id')->index();
            $table->string('loan_no', 30)->unique()->comment('Format: L-YYYYSocietyId+0001');
            $table->integer('society_id')->index();
            $table->integer('block_flat_id')->index();
            $table->decimal('requested_loan_amount', 10, 2);
            $table->decimal('loan_amount', 10, 2);
            $table->decimal('outstanding_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2);
            $table->decimal('interest_rate', 10, 2);
            $table->string('loan_description', 400);
            $table->string('approval_description', 400)->nullable();
            $table->integer('loan_status')->enum([1, 2, 3, 4])->comment('1 - Approved, 2 - Rejected, 3 - Pending, 4 - Closed')->index();
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
        Schema::dropIfExists('LoanRequest');
    }
};
