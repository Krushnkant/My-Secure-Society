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
        Schema::create('payment_transaction', function (Blueprint $table) {
            $table->bigIncrements('payment_transaction_id')->index();
            $table->integer('invoice_id')->index();
            $table->string('transaction_no', 200)->nullable();
            $table->string('payment_currency', 20);
            $table->decimal('transaction_amount', 10, 2);
            $table->string('gateway_name', 100)->nullable();
            $table->integer('payment_mode')->enum([1, 2])->comment('1 - Offline, 2 - Online, 3 - Cheque')->index();
            $table->integer('payment_status')->enum([1, 2, 3, 4, 5, 6])->default(1)->comment('1 - Paid, 2 - Failed, 3 - Due, 4 - Pending, 5 - In Hold, 6 - Refund')->index();
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
        Schema::dropIfExists('payment_transaction');
    }
};
