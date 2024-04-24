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
        Schema::create('resident_designate_auth', function (Blueprint $table) {
            $table->bigIncrements('resident_designate_auth_id')->index();
            $table->integer('resident_designation_id')->index();
            $table->integer('eauthority')->enum([6, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40]);
            $table->integer('can_view')->enum([0, 1, 2])->default(0)->comment('0 - Disabled, 1 - True, 2 - False');
            $table->integer('can_add')->enum([0, 1, 2])->default(0)->comment('0 - Disabled, 1 - True, 2 - False');
            $table->integer('can_edit')->enum([0, 1, 2])->default(0)->comment('0 - Disabled, 1 - True, 2 - False');
            $table->integer('can_delete')->enum([0, 1, 2])->default(0)->comment('0 - Disabled, 1 - True, 2 - False');
            $table->integer('can_print')->enum([0, 1, 2])->default(0)->comment('0 - Disabled, 1 - True, 2 - False');
            $table->dateTime('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('updated_by')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resident_designate_auth');
    }
};
