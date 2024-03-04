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
        Schema::create('company_designation_authority', function (Blueprint $table) {
            $table->bigIncrements('company_designation_authority_id')->index();
            $table->integer('company_designation_id')->index();
            $table->integer('eAuthority')->enum([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);
            $table->integer('can_view')->enum([0, 1, 2])->default(0)->comment('0 - Disabled, 1 - True, 2 - False');
            $table->integer('can_add')->enum([0, 1, 2])->default(0)->comment('0 - Disabled, 1 - True, 2 - False');
            $table->integer('can_edit')->enum([0, 1, 2])->default(0)->comment('0 - Disabled, 1 - True, 2 - False');
            $table->integer('can_delete')->enum([0, 1, 2])->default(0)->comment('0 - Disabled, 1 - True, 2 - False');
            $table->integer('can_print')->enum([0, 1, 2])->default(0)->comment('0 - Disabled, 1 - True, 2 - False');
            $table->integer('estatus')->enum([1, 2, 3, 4])->default(1)->comment('1 - Active, 2 - InActive, 3 - Delete, 4 - Pending')->index();
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
        Schema::dropIfExists('company_designation_authority');
    }
};
