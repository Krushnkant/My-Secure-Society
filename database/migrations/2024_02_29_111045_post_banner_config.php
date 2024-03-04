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
        Schema::create('post_banner_config', function (Blueprint $table) {
            $table->bigIncrements('post_banner_config_id')->index();
            $table->integer('block_flat_id')->index();
            $table->integer('create_banner_for')->enum([1, 2])->default(1)->comment('1 - Business, 2 - Personal Profile');
            $table->integer('master_item_id')->comment('Depends only CreateBannerFor (Value will be BusinessProfileId or SocietyMemberId)');
            $table->integer('is_display_mobile_no')->enum([1, 2])->default(1)->comment('1 - True, 2 - False');
            $table->integer('is_display_address')->enum([1, 2])->default(1)->comment('1 - True, 2 - False (Default false for Personal Profile)');
            $table->integer('estatus')->enum([1, 2, 3, 4])->default(1)->comment('1 - Active, 2 - InActive, 3 - Delete, 4 - Pending')->index();
            $table->dateTime('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('updated_by')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_banner_config');
    }
};
