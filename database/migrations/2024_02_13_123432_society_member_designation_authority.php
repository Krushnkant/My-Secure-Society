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
        Schema::create('SocietyMemberDesignationAuthority', function (Blueprint $table) {
            $table->bigIncrements('SocietyMemberDesignationAuthorityId')->index();
            $table->integer('SocietyMemberDesignationId')->index();
            $table->integer('eAuthority')->enum([6, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40]);
            $table->integer('canView')->enum([0, 1, 2])->default(0)->comment('0 - Disabled, 1 - True, 2 - False');
            $table->integer('canAdd')->enum([0, 1, 2])->default(0)->comment('0 - Disabled, 1 - True, 2 - False');
            $table->integer('canEdit')->enum([0, 1, 2])->default(0)->comment('0 - Disabled, 1 - True, 2 - False');
            $table->integer('canDelete')->enum([0, 1, 2])->default(0)->comment('0 - Disabled, 1 - True, 2 - False');
            $table->integer('canPrint')->enum([0, 1, 2])->default(0)->comment('0 - Disabled, 1 - True, 2 - False');
            $table->dateTime('UpdatedAt')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('UpdatedBy')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('SocietyMemberDesignationAuthority');
    }
};
