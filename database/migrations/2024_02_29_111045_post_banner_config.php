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
        Schema::create('PostBannerConfig', function (Blueprint $table) {
            $table->bigIncrements('PostBannerConfigId')->index();
            $table->integer('BlockFlatId')->index();
            $table->integer('CreateBannerFor')->enum([1, 2])->default(1)->comment('1 - Business, 2 - Personal Profile');
            $table->integer('MasterItemId')->comment('Depends only CreateBannerFor (Value will be BusinessProfileId or SocietyMemberId)');
            $table->integer('IsDisplayMobileNo')->enum([1, 2])->default(1)->comment('1 - True, 2 - False');
            $table->integer('IsDisplayAddress')->enum([1, 2])->default(1)->comment('1 - True, 2 - False (Default false for Personal Profile)');
            $table->integer('eStatus')->enum([1, 2, 3, 4])->default(1)->comment('1 - Active, 2 - InActive, 3 - Delete, 4 - Pending')->index();
            $table->dateTime('UpdatedAt')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('UpdatedBy')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PostBannerConfig');
    }
};
