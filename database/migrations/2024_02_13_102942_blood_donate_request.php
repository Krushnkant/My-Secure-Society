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
        Schema::create('blood_donate_request', function (Blueprint $table) {
            $table->bigIncrements('blood_donate_request_id')->index();
            $table->integer('society_id');
            $table->string('patient_name', 70);
            $table->integer('relation_with_patient')->enum([1, 2, 3, 4])->comment('1 - Own, 2 - Family, 3 - Relative, 4 - Other');
            $table->string('blood_group')->enum(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'])->nullable()->index();
            $table->integer('total_bottle')->default(1);
            $table->string('message')->nullable();
            $table->dateTime('expected_time');
            $table->string('hospital_name');
            $table->string('hospital_address');
            $table->string('landmark', 50);
            $table->integer('pin_code');
            $table->integer('city_id');
            $table->integer('state_id');
            $table->integer('country_id');
            $table->integer('request_status')->enum([1, 2, 3])->default(1)->comment('1 - Open, 2 - Closed, 3 - Deleted')->index();
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
        Schema::dropIfExists('blood_donate_request');
    }
};
