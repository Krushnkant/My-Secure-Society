<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    //  ConfigurationName & Value
    // 1 - Resident can Add Forum Topic? [1 - True, 2 - False]
    // 2 - Auto Generate Maintanance Invoice [1 - True, 2 - False]
    // 3 - Maintanance Invoice Generate Date
    // 4 - Maintanance Invoice Due Date
    // 5 - Auto Generate Loan Interest Invoice [1 - True, 2 - False]
    // 6 - Loan Interest Generate Date
    // 7 - Loan Interest Invoice Due Date
    
    public function up(): void
    {
        Schema::create('SocietyPanelConfiguration', function (Blueprint $table) {
            $table->bigIncrements('SocietyPanelConfigurationId')->index();
            $table->integer('society_id');
            $table->string('ConfigurationName');
            $table->string('ConfigurationValue');
            $table->dateTime('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('updated_by')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('SocietyPanelConfiguration');
    }
};
