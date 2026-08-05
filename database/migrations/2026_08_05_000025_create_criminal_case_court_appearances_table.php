<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stage 4-B — court appearances. Many per case (adjournments create
     * a new row via next_hearing_date rather than mutating history).
     */
    public function up(): void
    {
        Schema::create('criminal_case_court_appearances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('criminal_case_id')->constrained('criminal_cases')->cascadeOnDelete();

            $table->date('appearance_date');
            $table->time('appearance_time')->nullable();
            $table->string('court_name', 150);
            $table->string('room_number', 50)->nullable();
            $table->string('presiding_judge', 150)->nullable();
            $table->string('hearing_type', 30);
            $table->string('responsible_officer', 150)->nullable();
            $table->string('file_readiness_status', 30)->default('Preparing');
            $table->text('outcome')->nullable();
            $table->date('next_hearing_date')->nullable();

            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_case_court_appearances');
    }
};
