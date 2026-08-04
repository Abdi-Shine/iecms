<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Supporting tables for case registration: documents and the
     * activity/audit log. attorney_case_parties is created by
     * 2026_07_31_150002_create_attorney_case_parties_table.
     */
    public function up(): void
    {
        Schema::create('attorney_case_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attorney_case_id');
            $table->string('document_name', 150);
            $table->string('file_path');
            $table->date('document_date')->nullable();
            $table->string('uploaded_by', 100)->nullable();
            $table->timestamps();

            $table->foreign('attorney_case_id', 'ag_documents_case_id_fk')
                ->references('ACID')->on('attorney_cases')->onDelete('cascade');
        });

        Schema::create('attorney_case_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attorney_case_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type', 50);
            $table->text('description');
            $table->timestamps();

            $table->foreign('attorney_case_id', 'ag_activities_case_id_fk')
                ->references('ACID')->on('attorney_cases')->onDelete('cascade');
            $table->foreign('user_id', 'ag_activities_user_id_fk')
                ->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_activities');
        Schema::dropIfExists('attorney_case_documents');
    }
};
