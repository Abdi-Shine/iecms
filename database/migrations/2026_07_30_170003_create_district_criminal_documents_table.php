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
        Schema::create('district_criminal_documents', function (Blueprint $table) {
            $table->id('DID');
            $table->unsignedBigInteger('criminal_case_id');
            $table->string('document_name', 200);
            $table->date('document_date')->nullable();
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('addedBy', 50)->nullable();
            $table->string('addedDate', 20)->nullable();
            $table->string('updatedBy', 50)->nullable();
            $table->string('updatedDate', 20)->nullable();
            $table->timestamps();

            $table->foreign('criminal_case_id')->references('CMID')->on('district_criminal_registrations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('district_criminal_documents');
    }
};
