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
        Schema::create('announcement_file', function (Blueprint $table) {
            $table->bigIncrements('announcement_file_id')->index();
            $table->integer('announcement_id')->index();
            $table->integer('file_type')->enum([1, 2, 3, 4, 5])->default(1)->comment('1 - Image, 2 - Video, 3 - HTML, 4 - PDF, 5 - Other')->index();
            $table->string('file_url', 500);
            $table->dateTime('uploaded_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_file');
    }
};
