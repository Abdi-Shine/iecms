<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('district_civil_documents', function (Blueprint $table) {
            $table->id('DID');
            $table->unsignedBigInteger('civil_case_id');
            $table->string('document_name', 200);
            $table->date('document_date')->nullable();
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('addedBy', 50)->nullable();
            $table->string('addedDate', 20)->nullable();
            $table->string('updatedBy', 50)->nullable();
            $table->string('updatedDate', 20)->nullable();
            $table->timestamps();

            $table->foreign('civil_case_id')->references('CRID')->on('distric_civil_registrations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('civil_case_documents');
    }
};
