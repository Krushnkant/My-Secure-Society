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
        Schema::create('BloodDonateRequest', function (Blueprint $table) {
            $table->bigIncrements('BloodDonateRequestId')->index();
            $table->integer('society_id');
            $table->string('PatientName', 70);
            $table->integer('RelationwithPatient')->enum([1, 2, 3, 4])->comment('1 - Own, 2 - Family, 3 - Relative, 4 - Other');
            $table->string('BloodGroup')->enum(['A+, A-, B+, B-, O+, O-, AB+, AB-'])->nullable()->index();
            $table->integer('TotalBottle')->default(1);
            $table->string('Message')->nullable();
            $table->dateTime('ExpectedTime');
            $table->string('HospitalName');
            $table->string('HospitalAddress');
            $table->string('Landmark', 50);
            $table->integer('PinCode');
            $table->integer('city_id');
            $table->integer('state_id');
            $table->integer('country_id');
            $table->integer('RequestStatus')->enum([1, 2, 3])->default(1)->comment('1 - Open, 2 - Closed, 3 - Deleted')->index();
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
        Schema::dropIfExists('BloodDonateRequest');
    }
};
