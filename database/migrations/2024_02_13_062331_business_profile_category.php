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
        Schema::create('BusinessProfileCategory', function (Blueprint $table) {
            $table->bigIncrements('BusinessProfileCategoryId')->index();
            $table->integer('BusinessProfileId')->index();
            $table->integer('BusinessCategoryId')->index();
            $table->integer('IsPrimary')->default(2)->index()->comment('1 - True, 2 - False');
            $table->dateTime('UpdatedAt')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('UpdatedBy')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('BusinessProfileCategory');
    }
};
