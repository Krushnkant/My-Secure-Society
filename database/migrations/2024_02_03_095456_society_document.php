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
            $table->integer('society_id')->index();
            $table->integer('DocumentFolderId')->comment('Document is uploaded at Outside If value is 0')->index();
            $table->integer('DocumentType')->enum([1, 2])->default(1)->comment('1 - Society [Visible to only Society Admin or All Society Member or Selected Flat], 2 - Personal [Visible only Owner]')->index();
            $table->string('DocumentName', 100);
            $table->string('Note')->nullable();
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
        Schema::dropIfExists('SocietyDocument');
    }
};
