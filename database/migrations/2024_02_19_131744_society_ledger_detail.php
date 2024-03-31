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
        Schema::create('society_ledger_detail', function (Blueprint $table) {
            $table->bigIncrements('society_ledger_detail_id')->index();
            $table->integer('society_id')->index();
            $table->decimal('current_disbursed_loan_amount', 10, 2);
            $table->decimal('current_balance', 10, 2);
            $table->decimal('total_balance', 10, 2);
            $table->integer('estatus')->enum([1, 2, 3, 4])->default(1)->comment('1 - Active, 2 - InActive, 3 - Delete, 4 - Pending')->index();
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
        Schema::dropIfExists('society_ledger_detail');
    }
};

