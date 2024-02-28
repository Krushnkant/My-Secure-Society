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
        Schema::create('Country', function (Blueprint $table) {
            $table->bigIncrements('CountryId')->index();
            $table->string('CountryCode', 30);
            $table->string('CountryName', 60);
            $table->integer('PhoneCode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Country');
    }
};
