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
        Schema::create('society_charge', function (Blueprint $table) {
            $table->bigIncrements('society_charge_id')->index();
            $table->integer('society_id')->index();
            $table->integer('is_penalty')->default(1)->comment('1 - True, 2 - False')->index();
            $table->integer('charge_type')->enum([1, 2, 4, 5, 7])->default(1)->comment('1 - Maintenance, 2 - Loan Interest, 4 - Move In, 5 - Move Out, 7 - Payment')->index();
            $table->string('charge_name', 100);
            $table->integer('amount_type')->enum([1, 2])->default(2)->comment('1 - Percentage on Invoice Total Amount, 2 - Fix Amount')->index();
            $table->decimal('charge_amount', 10, 2);
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
        Schema::dropIfExists('society_charge');
    }
};
