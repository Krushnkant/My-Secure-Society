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
        Schema::create('ServiceRequest', function (Blueprint $table) {
            $table->bigIncrements('ServiceRequestId')->index();
            $table->integer('society_id')->index();
            $table->integer('society_block_id')->index();
            $table->integer('ServiceCategoryId')->index();
            $table->integer('AssignedToStaffMemberId')->index();
            $table->string('ServiceRequestNumber', 30)->unique()->comment('Format: SR-YYYYSocietyId+0001');
            $table->string('ServiceSubject', 200);
            $table->string('ServiceDescription', 1000);
            // $table->integer('ServicePriority')->enum([1, 2, 3])->default(2)->comment('1 - High, 2 - Medium, 3 - Low')->index();
            $table->integer('ServiceRequestStatus')->enum([1, 2, 3, 4, 5])->default(1)->comment('1 - Closed, 2 - Issue Raised, 3 - In Progress, 4 - ReOpened, 5 - In Hold')->index();
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
        Schema::dropIfExists('ServiceRequest');
    }
};
