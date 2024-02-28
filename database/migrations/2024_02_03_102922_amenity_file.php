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
        Schema::create('AmenityFile', function (Blueprint $table) {
            $table->bigIncrements('AmenityFileId')->index();
            $table->integer('AmenityId')->index();
            $table->integer('FileType')->enum([1, 2, 4])->default(1)->comment('1 - Image, 2 - Video, 4 - PDF')->index();
            $table->string('FileUrl', 500);
            $table->dateTime('UploadedAt')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('AmenityFile');
    }
};
