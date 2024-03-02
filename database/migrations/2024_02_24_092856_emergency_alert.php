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
        Schema::create('EmergencyAlert', function (Blueprint $table) {
            $table->bigIncrements('EmergencyAlertId')->index();
            $table->integer('AlertReasonType')->index()->comment('1 - Fire, 2 - Stuck in Lift, 3 - Animal Threat, 4 - Other');
            $table->string('AlertMessage');
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('EmergencyAlert');
    }
};
