<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('society_visitor_file', function (Blueprint $table) {
            $table->bigIncrements('society_visitor_file_id')->index();
            $table->integer('society_visitor_id')->index();
            $table->integer('file_type')->enum([1])->default(1)->comment('1 - Image')->index();
            $table->string('file_url', 500);
            $table->dateTime('uploaded_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('society_visitor_file');
    }
};
