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
        Schema::create('daily_help_provider_review', function (Blueprint $table) {
            $table->bigIncrements('daily_help_provider_review_id')->index();
            $table->integer('given_by_bloack_flat_id')->index();
            $table->integer('daily_help_provider_id')->index();
            $table->float('number_of_star')->index();
            $table->string('review_text', 200);
            $table->integer('review_status')->enum([1, 2, 3, 4])->default(1)->comment('1 - Approved, 2 - Rejected, 3 - Delete, 4 - Pending')->index();
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by')->index();
            $table->dateTime('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('updated_by')->index();
            $table->softDeletes('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_help_providerReview');
    }
};
