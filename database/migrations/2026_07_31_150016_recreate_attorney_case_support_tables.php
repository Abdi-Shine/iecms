<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Supporting tables for the rebuilt case registration pages: parties
     * involved in a case, supporting documents, and the activity/audit log.
     * Investigation, prosecutor, proceedings, and evidence management stay
     * removed per the earlier cleanup — this only restores case registration.
     */
    public function up(): void
    {
        Schema::create('attorney_case_parties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attorney_case_id');
            $table->string('party_role', 50);
            $table->string('full_name', 150);
            $table->string('contact_number', 30)->nullable();
            $table->string('national_id', 50)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();

            $table->foreign('attorney_case_id', 'ag_parties_case_id_fk')
                ->references('ACID')->on('attorney_cases')->onDelete('cascade');
        });

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
        Schema::dropIfExists('attorney_case_parties');
    }
};
