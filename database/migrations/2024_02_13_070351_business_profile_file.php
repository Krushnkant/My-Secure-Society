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
        Schema::create('BusinessProfileFile', function (Blueprint $table) {
            $table->bigIncrements('BusinessProfileFileId')->index();
            $table->integer('BusinessProfileId')->index();
            $table->integer('FileType')->enum([1, 4, 5])->comment('1 - Image, 4 - PDF')->index();
            $table->string('FileUrl', 500);
            $table->dateTime('UploadedAt')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('BusinessProfileFile');
    }
};
