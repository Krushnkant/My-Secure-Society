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
        Schema::create('EmergencyContact', function (Blueprint $table) {
            $table->bigIncrements('EmergencyContactId')->index();
            $table->integer('ContactType')->index()->comment('1 - Government Help, 2 - Society Emergency Contact, 3 - Personal');
            $table->integer('user_id')->index()->comment('0 for Government Help & Society Emergency Contact');
            $table->string('Name', 100);
            $table->string('MobileNo', 15);
            $table->integer('estatus')->enum([1, 2, 3, 4])->default(1)->comment('1 - Active, 2 - InActive, 3 - Delete, 4 - Pending')->index();
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
        Schema::dropIfExists('UserEmergencyContact');
    }
};
