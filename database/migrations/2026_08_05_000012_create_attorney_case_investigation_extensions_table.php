<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_case_investigation_extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attorney_case_id')->constrained('attorney_cases', 'ACID')->cascadeOnDelete();

            $table->date('current_deadline')->nullable();
            $table->string('requested_days')->nullable();
            $table->date('new_deadline');
            $table->text('justification')->nullable();
            $table->string('requesting_prosecutor')->nullable();
            $table->date('request_date');
            $table->string('supporting_document_path')->nullable();

            $table->string('status')->default('Sugaya');
            $table->text('approval_reason')->nullable();
            $table->string('approved_by')->nullable();
            $table->date('approved_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_investigation_extensions');
    }
};
