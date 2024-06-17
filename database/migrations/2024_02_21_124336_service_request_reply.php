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
        // Schema::create('service_request_reply', function (Blueprint $table) {
        //     $table->bigIncrements('service_request_reply_id')->index();
        //     $table->integer('service_request_id')->index();
        //     $table->string('reply_description', 500);
        //     $table->dateTime('created_at')->nullable();
        //     $table->integer('created_by')->index();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('service_request_reply');
    }
};

