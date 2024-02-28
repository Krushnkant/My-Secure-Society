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
        Schema::create('OrderPayment', function (Blueprint $table) {
            $table->bigIncrements('OrderPaymentId')->index();
            $table->integer('SubscriptionOrderId')->index();
            $table->integer('PaymentType')->enum([1, 2])->default(1)->comment('1 - Offline, 2 - Online, 3 - Cheque')->index();
            $table->decimal('AmountPaid', 10, 2);
            $table->string('PaymentNote')->nullable();
            $table->dateTime('PaymentDate')->index();
            $table->dateTime('CreatedAt')->nullable();
            $table->integer('CreatedBy')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('OrderPayment');
    }
};
