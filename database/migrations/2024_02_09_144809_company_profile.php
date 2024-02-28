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
        Schema::create('CompanyProfile', function (Blueprint $table) {
            $table->bigIncrements('CompanyProfileId')->index();
            $table->string('CompanyName');
            $table->string('LogoUrl');
            $table->string('GstInNumber');
            $table->string('StreetAddress1');
            $table->string('StreetAddress2')->nullable();
            $table->string('Landmark', 50);
            $table->string('PinCode', 10);
            $table->decimal('Latitude', 10, 8)->nullable();
            $table->decimal('Longitude', 11, 8)->nullable();
            $table->integer('CityId');
            $table->integer('StateId');
            $table->integer('CountryId');
            $table->dateTime('UpdatedAt')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('UpdatedBy')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('CompanyProfile');
    }
};
