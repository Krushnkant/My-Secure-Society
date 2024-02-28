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
        Schema::create('LoanRequest', function (Blueprint $table) {
            $table->bigIncrements('LoanRequestId')->index();
            $table->string('LoanNo', 30)->unique()->comment('Format: L-YYYYSocietyId+0001');
            $table->integer('SocietyId')->index();
            $table->integer('BlockFlatId')->index();
            $table->decimal('RequestedLoanAmount', 10, 2);
            $table->decimal('LoanAmount', 10, 2);
            $table->decimal('OutstandingAmount', 10, 2);
            $table->decimal('PaidAmount', 10, 2);
            $table->decimal('InterestRate', 10, 2);
            $table->string('LoanDescription', 400);
            $table->string('ApprovalDescription', 400)->nullable();
            $table->integer('LoanStatus')->enum([1, 2, 3, 4])->comment('1 - Approved, 2 - Rejected, 3 - Pending, 4 - Closed')->index();
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
        Schema::dropIfExists('LoanRequest');
    }
};
