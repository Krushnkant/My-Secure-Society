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
        Schema::create('daily_help_provider_file', function (Blueprint $table) {
            $table->bigIncrements('daily_help_provider_file_id')->index();
            $table->integer('daily_help_provider_id')->index();
            $table->integer('file_view')->enum([1, 4, 5])->comment('1 - Front Side, 2 - Back Side')->index();
            $table->integer('file_type')->enum([1])->comment('1 - Image')->index();
            $table->string('file_url', 500);
            $table->dateTime('uploaded_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_help_providerFile');
    }
};
