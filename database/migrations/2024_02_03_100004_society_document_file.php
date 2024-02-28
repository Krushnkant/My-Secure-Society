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
        Schema::create('SocietyDocumentFile', function (Blueprint $table) {
            $table->bigIncrements('SocietyDocumentFileId')->index();
            $table->integer('SocietyDocumentId')->index();
            $table->integer('FileType')->enum([1, 4, 5])->comment('1 - Image, 4 - PDF, 5 - Other')->index();
            $table->string('FileUrl', 500);
            $table->dateTime('UploadedAt')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('SocietyDocumentFile');
    }
};
