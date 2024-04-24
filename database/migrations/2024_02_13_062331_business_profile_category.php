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
        Schema::create('business_profile_category', function (Blueprint $table) {
            $table->bigIncrements('business_profile_category_id')->index();
            $table->integer('business_profile_id')->index();
            $table->integer('business_category_id')->index();
            $table->integer('is_primary')->default(2)->index()->comment('1 - True, 2 - False');
            $table->dateTime('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('updated_by')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_profile_category');
    }
};
