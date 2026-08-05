<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_case_warrant_of_arrests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attorney_case_id')->constrained('attorney_cases', 'ACID')->cascadeOnDelete();

            $table->string('suspect_name')->nullable();
            $table->string('suspect_address')->nullable();
            $table->text('offence_alleged')->nullable();
            $table->string('legal_provision')->nullable();
            $table->text('supporting_evidence_summary')->nullable();
            $table->date('application_date');
            $table->string('applying_prosecutor')->nullable();
            $table->string('court_name')->nullable();
            $table->string('judge_name')->nullable();
            $table->string('warrant_status')->default('Sugaya');
            $table->string('warrant_number')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('special_conditions')->nullable();
            $table->string('warrant_document_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_warrant_of_arrests');
    }
};
