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
        Schema::create('society_member', function (Blueprint $table) {
            $table->bigIncrements('society_member_id')->index();
            $table->integer('parent_society_member_id')->index();
            $table->integer('user_id')->index();
            $table->integer('block_flat_id')->index();
            $table->integer('resident_designation_id')->index();
            $table->integer('society_department_id')->nullable()->comment('Null for Not Member of any Department');
            $table->integer('resident_type')->enum([1, 2])->comment('1 - Tenant, 2 - Owner')->index();
            $table->integer('estatus')->enum([1, 2, 3, 4, 5])->default(1)->comment('1 - Active, 2 - InActive, 3 - Delete, 4 - Pending, 5 - Rejected')->index();
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
        Schema::dropIfExists('society_member');
    }
};
