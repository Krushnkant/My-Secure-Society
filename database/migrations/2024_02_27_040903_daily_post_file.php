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
        Schema::create('daily_post_file', function (Blueprint $table) {
            $table->bigIncrements('daily_post_file_id')->index();
            $table->integer('society_daily_post_id')->index();
            $table->enum('file_type', [1, 2, 3, 4, 5])->comment('1 - Image, 2 - Video, 3 - HTML, 4 - PDF, 5 - Other')->index();
            $table->string('file_url', 500);
            $table->dateTime('uploaded_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_post_file');
    }
};
