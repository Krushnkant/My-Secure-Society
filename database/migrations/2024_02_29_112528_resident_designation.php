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
        Schema::create('ResidentDesignation', function (Blueprint $table) {
            $table->bigIncrements('ResidentDesignationId')->index();
            $table->integer('SocietyId')->comment('0 for Common Designation for all Society')->index();
            $table->string('DesignationName', 30);
            $table->integer('canUpdateAuthorityClaims')->enum([1, 2])->default(1)->comment('1 - True, 2 - False')->index();
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
        Schema::dropIfExists('ResidentDesignation');
    }
};
