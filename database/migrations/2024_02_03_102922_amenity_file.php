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
        Schema::create('amenity_file', function (Blueprint $table) {
            $table->bigIncrements('amenity_file_id')->index();
            $table->integer('amenity_id')->index();
            $table->integer('file_type')->enum([1, 2, 4])->default(1)->comment('1 - Image, 2 - Video, 4 - PDF')->index();
            $table->string('file_url', 500);
            $table->dateTime('uploaded_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amenity_file');
    }
};
