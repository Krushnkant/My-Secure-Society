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
        Schema::create('daily_post_pole_option', function (Blueprint $table) {
            $table->bigIncrements('daily_post_pole_option_id')->index();
            $table->integer('society_daily_post_id')->index();
            $table->string('option_text', 150);
            $table->integer('total_vote');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_post_pole_option');
    }
};
