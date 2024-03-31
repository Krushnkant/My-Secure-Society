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
        Schema::create('daily_post_pole_option_vote', function (Blueprint $table) {
            $table->bigIncrements('daily_post_pole_option_vote_id');
            $table->integer('society_daily_post_pole_option_id');
            $table->integer('user_id');
            $table->dateTime('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_post_pole_option_vote');
    }
};

