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
        Schema::create('DailyPostPoleOption', function (Blueprint $table) {
            $table->bigIncrements('DailyPostPoleOptionId')->index();
            $table->integer('SocietyDailyPostId')->index();
            $table->string('OptionText', 150);
            $table->integer('TotalVote');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('DailyPostPoleOption');
    }
};
