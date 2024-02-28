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
        Schema::create('SocietyDocument', function (Blueprint $table) {
            $table->bigIncrements('SocietyDocumentId')->index();
            $table->integer('SocietyId')->index();
            $table->integer('DocumentFolderId')->comment('Document is uploaded at Outside If value is 0')->index();
            $table->integer('DocumentType')->enum([1, 2])->default(1)->comment('1 - Society [Visible to only Society Admin or All Society Member or Selected Flat], 2 - Personal [Visible only Owner]')->index();
            $table->string('DocumentName', 100);
            $table->string('Note')->nullable();
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
        Schema::dropIfExists('SocietyDocument');
    }
};
