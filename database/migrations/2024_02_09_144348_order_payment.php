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
        Schema::create('order_payment', function (Blueprint $table) {
            $table->bigIncrements('order_payment_id')->index();
            $table->integer('subscription_order_id')->index();
            $table->integer('payment_type')->enum([1, 2])->default(1)->comment('1 - Offline, 2 - Online, 3 - Cheque')->index();
            $table->decimal('amount_paid', 10, 2);
            $table->string('payment_note')->nullable();
            $table->date('payment_date')->index();
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payment');
    }
};
