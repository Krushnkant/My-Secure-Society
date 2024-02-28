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
        Schema::create('SocietyMember', function (Blueprint $table) {
            $table->bigIncrements('SocietyMemberId')->index();
            $table->integer('ParentSocietyMemberId')->index();
            $table->integer('UserId')->index();
            $table->integer('BlockFlatId')->index();
            $table->integer('SocietyMemberDesignationId')->index();
            $table->integer('SocietyDepartmentId')->nullable()->comment('Null for Not Member of any Department');
            $table->integer('ResidentType')->enum([1, 2])->comment('1 - Tenant, 2 - Owner')->index();
            $table->integer('eStatus')->enum([1, 2, 3, 4, 5])->default(1)->comment('1 - Active, 2 - InActive, 3 - Delete, 4 - Pending, 5 - Rejected')->index();
            $table->dateTime('CreatedAt')->nullable();
            $table->integer('CreatedBy')->index();
            $table->dateTime('UpdatedAt')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('UpdatedBy')->index();
            $table->softDeletes('DeletedAt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('SocietyMember');
    }
};
