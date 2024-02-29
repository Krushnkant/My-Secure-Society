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
        Schema::create('DocumentSharedFlat', function (Blueprint $table) {
            $table->bigIncrements('DocumentSharedFlatId')->index();
            $table->integer('BlockFlatId')->index();
            $table->integer('SocietyDocumentId')->index();
            $table->dateTime('UpdatedAt')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('UpdatedBy')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('DocumentSharedFlat');
    }
};
