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
        Schema::create('push_notification', function (Blueprint $table) {
            $table->bigIncrements('society_id')->index();
            $table->string('notification_title');
            $table->string('notify_description');
            $table->string('notify_image', 500);
            $table->integer('notification_type')->comment('1 - None, 2 - Custom URL, 3 - Maintanance, 4 - Utility Bill, 5 - Amenity List, 6 - Amenity Detail');
            $table->integer('masterItemid')->comment('Depends only Notification Type');
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
        Schema::dropIfExists('push_notification');
    }
};
