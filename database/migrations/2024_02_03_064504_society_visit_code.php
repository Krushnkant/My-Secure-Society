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
        Schema::create('SocietyVisitCode', function (Blueprint $table) {
            $table->bigIncrements('SocietyVisitCodeId')->index();
            $table->integer('VisitorGatepassId')->index();
            $table->integer('VisitCode')->unique();
            $table->integer('VerificationStatus')->enum([1, 2, 3])->default(1)->comment('1 - Verified, 2 - Pending, 3 - Cancelled')->index();
            $table->dateTime('ValidFromTime');
            $table->dateTime('ValidToTime');
            $table->dateTime('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('updated_by')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('SocietyVisitCode');
    }
};
