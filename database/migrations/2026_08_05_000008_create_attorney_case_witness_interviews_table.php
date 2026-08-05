<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_case_witness_interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attorney_case_id')->constrained('attorney_cases', 'ACID')->cascadeOnDelete();

            $table->string('witness_name')->nullable();
            $table->string('witness_contact')->nullable();
            $table->string('relationship_to_case')->nullable();
            $table->date('interview_date');
            $table->string('interview_location')->nullable();
            $table->string('interviewing_officer')->nullable();
            $table->text('testimony_summary')->nullable();
            $table->string('credibility_assessment')->nullable();
            $table->boolean('follow_up_needed')->default(false);
            $table->string('statement_file_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_witness_interviews');
    }
};
