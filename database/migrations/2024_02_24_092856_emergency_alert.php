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
        Schema::create('emergency_alert', function (Blueprint $table) {
            $table->bigIncrements('emergency_alert_id')->index();
            $table->integer('alert_reason_type')->index()->comment('1 - Fire, 2 - Stuck in Lift, 3 - Animal Threat, 4 - Other');
            $table->string('alert_message');
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_alert');
    }
};

