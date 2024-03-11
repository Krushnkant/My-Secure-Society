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
        Schema::create('subscription_order', function (Blueprint $table) {
            $table->bigIncrements('subscription_order_id')->index();
            $table->integer('society_id')->index();
            $table->string('order_id');
            $table->integer('total_flat');
            $table->decimal('amount_per_flat', 10, 2);
            $table->decimal('sub_total_amount', 10, 2);
            $table->decimal('gst_percent', 10, 2);
            $table->decimal('gst_amount', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('total_paid_amount', 10, 2);
            $table->decimal('total_outstanding_amount', 10, 2);
            $table->integer('order_status')->enum([1, 2, 3])->default(1)->comment('1 - Pending, 2 - In Progress, 3 - Completed, 4 - Cancelled')->index();
            $table->date('due_date')->index();
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
        Schema::dropIfExists('subscription_order');
    }
};
