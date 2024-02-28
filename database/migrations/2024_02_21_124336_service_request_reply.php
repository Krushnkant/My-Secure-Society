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
        Schema::create('ServiceRequestReply', function (Blueprint $table) {
            $table->bigIncrements('ServiceRequestReplyId')->index();
            $table->integer('ServiceRequestId')->index();
            $table->string('ReplyDescription', 500);
            $table->dateTime('CreatedAt')->nullable();
            $table->integer('CreatedBy')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ServiceRequestReply');
    }
};
