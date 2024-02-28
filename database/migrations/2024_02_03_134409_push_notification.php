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
        Schema::create('PushNotification', function (Blueprint $table) {
            $table->bigIncrements('SocietyId')->index();
            $table->string('NotificationTitle');
            $table->string('NotifyDescription');
            $table->string('NotifyImage', 500);
            $table->integer('NotificationType')->comment('1 - None, 2 - Custom URL, 3 - Maintanance, 4 - Utility Bill, 5 - Amenity List, 6 - Amenity Detail');
            $table->integer('MasterItemId')->comment('Depends only Notification Type');
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
        Schema::dropIfExists('PushNotification');
    }
};
