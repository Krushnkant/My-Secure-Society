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
        Schema::create('society_visit_code', function (Blueprint $table) {
            $table->bigIncrements('society_visit_code_id')->index();
            $table->integer('visitor_gatepass_id')->index();
            $table->integer('visit_code')->unique();
            $table->integer('verification_status')->enum([1, 2, 3])->default(1)->comment('1 - Verified, 2 - Pending, 3 - Cancelled')->index();
            $table->dateTime('valid_from_time');
            $table->dateTime('valid_to_time');
            $table->dateTime('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('updated_by')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('society_visit_code');
    }
};
