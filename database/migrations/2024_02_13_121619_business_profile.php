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
        Schema::create('BusinessProfile', function (Blueprint $table) {
            $table->bigIncrements('BusinessProfileId')->index();
            $table->string('BusinessName', 100);
            $table->string('BusinessIcon')->nullable();
            $table->string('PhoneNumber', 15);
            $table->string('WebsiteUrl')->nullable();
            $table->string('Description', 500)->nullable();
            $table->string('StreetAddress1');
            $table->string('StreetAddress2')->nullable();
            $table->string('Landmark', 50);
            $table->string('PinCode', 10)->index();
            $table->decimal('Latitude', 10, 8)->nullable();
            $table->decimal('Longitude', 11, 8)->nullable();
            $table->integer('CityId')->index();
            $table->integer('StateId')->index();
            $table->integer('CountryId');
            $table->integer('eStatus')->enum([1, 2, 3])->default(1)->index()->comment('1 - Active, 2 - InActive, 3 - Delete');
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
        Schema::dropIfExists('BusinessProfile');
    }
};
