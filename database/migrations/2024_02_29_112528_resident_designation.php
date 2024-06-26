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
        Schema::create('resident_designation', function (Blueprint $table) {
            $table->bigIncrements('resident_designation_id')->index();
            $table->integer('society_id')->comment('0 for Common Designation for all Society')->index();
            $table->string('designation_name', 30);
            $table->integer('can_update_authority_claims')->enum([1, 2])->default(1)->comment('1 - True, 2 - False')->index();
            $table->integer('estatus')->enum([1, 2, 3, 4])->default(1)->comment('1 - Active, 2 - InActive, 3 - Delete, 4 - Pending')->index();
            $table->integer('use_for')->enum([1, 2])->default(1)->comment('1 - Resident, 2 - Staff Member')->index();
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
        Schema::dropIfExists('resident_designation');
    }
};
