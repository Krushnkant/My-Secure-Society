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
        Schema::create('Amenity', function (Blueprint $table) {
            $table->bigIncrements('AmenityId')->index();
            $table->integer('society_id')->index();
            $table->string('AmenityName', 100);
            $table->string('AmenityDescription', 500)->nullable();
            $table->integer('IsChargeable')->enum([1, 2])->default(1)->comment('1 - True, 2 - false')->index();
            $table->integer('ApplicablePaymentType')->enum([1, 2, 3])->default(1)->comment('1 - Offline, 2 - Online, 3 - Other')->index();
            $table->integer('eStatus')->enum([1, 2, 3, 4])->default(1)->comment('1 - Active, 2 - InActive, 3 - Delete, 4 - Pending')->index();
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
        Schema::dropIfExists('Amenity');
    }
};
