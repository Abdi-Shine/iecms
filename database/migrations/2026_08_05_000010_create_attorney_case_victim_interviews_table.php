<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_case_victim_interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attorney_case_id')->constrained('attorney_cases', 'ACID')->cascadeOnDelete();

            $table->string('victim_name')->nullable();
            $table->date('interview_date');
            $table->time('interview_time')->nullable();
            $table->string('interview_location')->nullable();
            $table->string('interviewing_officer')->nullable();
            $table->boolean('support_person_present')->default(false);
            $table->string('support_person_name')->nullable();
            $table->text('victim_impact_summary')->nullable();
            $table->boolean('medical_treatment_required')->default(false);
            $table->boolean('protective_measures_needed')->default(false);
            $table->text('protective_measures_notes')->nullable();
            $table->string('statement_file_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_victim_interviews');
    }
};
