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
        Schema::create('PaymentTransaction', function (Blueprint $table) {
            $table->bigIncrements('PaymentTransactionId')->index();
            $table->integer('InvoiceId')->index();
            $table->string('TransactionNo', 200)->nullable();
            $table->string('PaymentCurrency', 20);
            $table->decimal('TransactionAmount', 10, 2);
            $table->string('GatewayName', 100)->nullable();
            $table->integer('PaymentMode')->enum([1, 2])->comment('1 - Offline, 2 - Online, 3 - Cheque')->index();
            $table->integer('PaymentStatus')->enum([1, 2, 3, 4, 5, 6])->default(1)->comment('1 - Paid, 2 - Failed, 3 - Due, 4 - Pending, 5 - In Hold, 6 - Refund')->index();
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
        Schema::dropIfExists('PaymentTransaction');
    }
};
