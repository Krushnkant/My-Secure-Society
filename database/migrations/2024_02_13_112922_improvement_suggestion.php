<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    // Feature eNum Type
    // 1 - Notice Board
    // 2 - Residents
    // 3 - Gatepass & Visitor Entry
    // 4 - Delivered At Gate
    // 5 - Local Directory
    // 6 - Communications (Forum)
    // 7 - Amenities
    // 8 - MoveIn / MoveOut
    // 9 - Documents (Society Documents / Personal)
    // 10 - Business Profile
    // 11 - Security Alert
    // 12 - Emergency Contacts (Max 3)
    // 13 - Donate Blood 
    // 14 - Daily Services
    // 15 - Maintanance
    // 16 - Loan
    // 17 - Utility Payments
    // 18 - Department
    // 19 - Category
    // 20 - Staff Management
    // 21 - Vendor (Service Providers)
    // 22 - Service Request (Work assign to Staff Member - Auto/Manual)
    // 23 - Help & Support
    
    public function up(): void
    {
        Schema::create('ImprovementSuggestion', function (Blueprint $table) {
            $table->bigIncrements('ImprovementSuggestionId')->index();
            $table->integer('user_id')->index();
            $table->string('Message');
            $table->integer('Feature')->enum([1])->default(1)->comment('1 - Blood Donate, 2 - Amenity, 3 - Maintanance, 4 - Staff Management')->index();
            $table->dateTime('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->integer('updated_by')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ImprovementSuggestion');
    }
};
