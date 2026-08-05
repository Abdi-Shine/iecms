<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_case_suspect_interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attorney_case_id')->constrained('attorney_cases', 'ACID')->cascadeOnDelete();

            $table->string('suspect_name')->nullable();
            $table->date('interview_date');
            $table->time('interview_time')->nullable();
            $table->string('interview_location')->nullable();
            $table->string('interviewing_officer')->nullable();
            $table->boolean('interpreter_used')->default(false);
            $table->string('interpreter_name')->nullable();
            $table->boolean('legal_counsel_present')->default(false);
            $table->string('counsel_name')->nullable();
            $table->boolean('rights_informed')->default(false);
            $table->string('recording_method')->nullable();
            $table->text('statement_summary')->nullable();
            $table->boolean('statement_voluntary')->default(false);
            $table->boolean('signature_obtained')->default(false);
            $table->string('recording_file_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_suspect_interviews');
    }
};
