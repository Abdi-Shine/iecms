<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appeal_family_documents', function (Blueprint $table) {
            $table->id('DID');
            $table->unsignedBigInteger('family_case_id');
            $table->string('document_name', 200);
            $table->date('document_date')->nullable();
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('addedBy', 50)->nullable();
            $table->string('addedDate', 20)->nullable();
            $table->string('updatedBy', 50)->nullable();
            $table->string('updatedDate', 20)->nullable();
            $table->timestamps();

            $table->foreign('family_case_id')->references('AFCID')->on('appeal_family_registrations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appeal_family_documents');
    }
};
