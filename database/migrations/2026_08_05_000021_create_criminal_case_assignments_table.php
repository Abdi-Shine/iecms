<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stage 3-A of the Investigation Workflow — formal case assignment.
     * One per case; unlike the Occurrence Book's assignment field (which
     * is a quick initial routing choice), this is the deliberate,
     * confirmed assignment with an investigation plan that marks Stage 3
     * complete and unlocks Stage 4.
     */
    public function up(): void
    {
        Schema::create('criminal_case_assignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('criminal_case_id')->constrained('criminal_cases')->cascadeOnDelete();
            $table->foreignId('assigned_investigator_id')->constrained('users')
                ->cascadeOnDelete()->name('cid_assignment_investigator_fk');
            $table->text('secondary_officers')->nullable();
            $table->text('investigation_plan')->nullable();
            $table->date('investigation_start_date');
            $table->date('target_completion_date')->nullable();
            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_case_assignments');
    }
};
