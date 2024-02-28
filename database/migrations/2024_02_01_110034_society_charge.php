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
        Schema::create('SocietyCharge', function (Blueprint $table) {
            $table->bigIncrements('SocietyChargeId')->index();
            $table->integer('SocietyId')->index();
            $table->integer('isPenalty')->default(1)->comment('1 - True, 2 - False')->index();
            $table->integer('ChargeType')->enum([1, 2, 4, 5, 7])->default(1)->comment('1 - Maintenance, 2 - Loan Interest, 4 - Move In, 5 - Move Out, 7 - Payment')->index();
            $table->string('ChargeName', 100);
            $table->integer('AmountType')->enum([1, 2])->default(2)->comment('1 - Percentage on Invoice Total Amount, 2 - Fix Amount')->index();
            $table->decimal('ChargeAmount', 10, 2);
            $table->integer('eStatus')->enum([1, 2, 3, 4])->default(1)->comment('1 - Active, 2 - InActive, 3 - Delete, 4 - Pending')->index();
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
        Schema::dropIfExists('SocietyCharge');
    }
};
