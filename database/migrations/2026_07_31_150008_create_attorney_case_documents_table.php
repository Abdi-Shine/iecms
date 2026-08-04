<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_case_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attorney_case_id');
            $table->string('document_name', 150);
            $table->date('document_date')->nullable();
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();

            $table->foreign('attorney_case_id', 'ag_case_documents_case_id_fk')
                ->references('ACID')->on('attorney_cases')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_documents');
    }
};
