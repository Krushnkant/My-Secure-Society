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
        Schema::create('service_request', function (Blueprint $table) {
            $table->bigIncrements('service_request_id')->index();
            $table->integer('parent_service_request_id')->index()->comment('0 for Service Request or Non Zero for Service Request Reply');
            $table->integer('society_id')->index();
            $table->integer('society_block_id')->index();
            $table->integer('service_category_id')->index();
            $table->integer('assigned_to_staff_member_id')->index();
            $table->string('service_request_number', 30)->unique()->comment('Format: SR-YYYYSocietyId+0001');
            $table->string('service_subject', 200);
            $table->string('service_description', 1000);
            // $table->integer('service_priority')->enum([1, 2, 3])->default(2)->comment('1 - High, 2 - Medium, 3 - Low')->index();
            $table->integer('service_request_status')->enum([1, 2, 3, 4, 5])->default(1)->comment('1 - Closed, 2 - Issue Raised, 3 - In Progress, 4 - Opened, 5 - In Hold')->index();
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
        Schema::dropIfExists('service_request');
    }
};
