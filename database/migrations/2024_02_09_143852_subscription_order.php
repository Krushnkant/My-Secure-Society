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
        Schema::create('SubscriptionOrder', function (Blueprint $table) {
            $table->bigIncrements('SubscriptionOrderId')->index();
            $table->integer('society_id')->index();
            $table->string('OrderId');
            $table->integer('TotalFlat');
            $table->decimal('AmountPerFlat', 10, 2);
            $table->decimal('SubTotalAmount', 10, 2);
            $table->decimal('GstPercent', 10, 2);
            $table->decimal('GstAmount', 10, 2);
            $table->decimal('TotalAmount', 10, 2);
            $table->decimal('TotalPaidAmount', 10, 2);
            $table->decimal('TotalOutstandingAmount', 10, 2);
            $table->integer('OrderStatus')->enum([1, 2, 3])->default(1)->comment('1 - Pending, 2 - In Progress, 3 - Completed, 4 - Cancelled')->index();
            $table->dateTime('DueDate')->index();
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
        Schema::dropIfExists('SubscriptionOrder');
    }
};
