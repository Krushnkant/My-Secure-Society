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
        Schema::create('DailyHelpProviderReview', function (Blueprint $table) {
            $table->bigIncrements('DailyHelpProviderReviewId')->index();
            $table->integer('GivenByBloackFlatId')->index();
            $table->integer('ReviewToUserId')->index();
            $table->float('NumberOfStar')->index();
            $table->string('ReviewText', 200);
            $table->integer('ReviewStatus')->enum([1, 2, 3, 4])->default(1)->comment('1 - Approved, 2 - Rejected, 3 - Delete, 4 - Pending')->index();
            $table->dateTime('CreatedAt')->nullable();
            $table->integer('CreatedBy')->index();
            $table->dateTime('UpdatedAt')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('UpdatedBy')->index();
            $table->softDeletes('DeletedAt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('DailyHelpProviderReview');
    }
};
