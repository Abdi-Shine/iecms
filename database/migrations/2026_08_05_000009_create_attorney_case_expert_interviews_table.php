<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_case_expert_interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attorney_case_id')->constrained('attorney_cases', 'ACID')->cascadeOnDelete();

            $table->string('expert_name')->nullable();
            $table->string('specialization')->nullable();
            $table->string('credentials')->nullable();
            $table->date('interview_date');
            $table->string('interview_location')->nullable();
            $table->string('interviewing_officer')->nullable();
            $table->text('expert_opinion_summary')->nullable();
            $table->boolean('report_attached')->default(false);
            $table->string('fee_arrangement')->nullable();
            $table->string('expert_report_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_expert_interviews');
    }
};
