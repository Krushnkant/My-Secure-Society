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
        Schema::create('ServiceCategory', function (Blueprint $table) {
            $table->bigIncrements('ServiceCategoryId')->index();
            $table->integer('SocietyId')->index();
            $table->integer('SocietyDepartmentId')->index();
            // $table->integer('ParentServiceCategoryId')->comment('0 For First Parent Category. Max upto 3')->index();
            $table->string('ServiceCategoryName', 50);
            $table->string('CategoryDescription', 400)->nullable();
            $table->integer('eStatus')->enum([1, 2, 3, 4])->default(1)->comment('1 - Active, 2 - InActive, 3 - Delete, 4 - Pending')->index();
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
        Schema::dropIfExists('ServiceCategory');
    }
};
