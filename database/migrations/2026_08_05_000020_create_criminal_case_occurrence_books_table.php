<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stage 2 of the Investigation Workflow — Occurrence Book. The
     * ob_number is generated server-side only (see CriminalCaseOccurrenceBook
     * booted() hook), same non-editable-on-the-client convention as
     * criminal_cases.case_number. Stage 3 stays locked until
     * assigned_investigator_id and supervisor_acknowledged_at are both set
     * (enforced in CriminalCaseWorkflowController, not here).
     */
    public function up(): void
    {
        Schema::create('criminal_case_occurrence_books', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('criminal_case_id')->constrained('criminal_cases')->cascadeOnDelete();

            $table->string('ob_number', 50)->unique();
            $table->dateTime('ob_datetime');
            $table->string('reporting_officer', 150);

            $table->string('offence_nature', 255);
            $table->dateTime('incident_datetime')->nullable();
            $table->string('incident_location', 255)->nullable();

            $table->string('complainant_name', 150)->nullable();
            $table->string('complainant_contact', 50)->nullable();
            $table->text('complainant_id')->nullable();
            $table->string('complainant_relationship', 100)->nullable();

            $table->text('suspect_details')->nullable();
            $table->text('narrative')->nullable();
            $table->text('witnesses')->nullable();
            $table->text('initial_evidence_noted')->nullable();

            $table->string('priority', 20)->default('Routine');

            $table->foreignId('assigned_investigator_id')->nullable()
                ->constrained('users')->nullOnDelete()->name('cid_ob_assigned_investigator_fk');
            $table->string('assigned_unit', 100)->nullable();

            $table->foreignId('supervisor_acknowledged_by')->nullable()
                ->constrained('users')->nullOnDelete()->name('cid_ob_supervisor_ack_fk');
            $table->timestamp('supervisor_acknowledged_at')->nullable();

            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_case_occurrence_books');
    }
};
