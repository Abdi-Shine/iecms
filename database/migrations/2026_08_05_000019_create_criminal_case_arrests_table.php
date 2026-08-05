<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stage 1 of the Investigation Workflow — Arrest (with/without
     * warrant). One arrest record per case for now, same hasOne shape
     * Attorney uses for its arrest sub-tables. arrestee_national_id is
     * widened to text and encrypted at the model layer, same convention
     * as AttorneyCaseParty::national_id.
     */
    public function up(): void
    {
        Schema::create('criminal_case_arrests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('criminal_case_id')->constrained('criminal_cases')->cascadeOnDelete();

            $table->string('arrest_type', 20); // with_warrant | without_warrant

            $table->string('arrestee_name', 150);
            $table->text('arrestee_national_id')->nullable();
            $table->date('arrestee_dob')->nullable();
            $table->string('arrestee_gender', 10)->nullable();
            $table->string('arrestee_nationality', 100)->nullable();
            $table->string('arrestee_address', 255)->nullable();
            $table->string('arrestee_contact', 50)->nullable();

            $table->string('arresting_officer_name', 150);
            $table->string('arresting_officer_badge', 50)->nullable();
            $table->string('arresting_officer_unit', 100)->nullable();

            $table->date('arrest_date');
            $table->time('arrest_time')->nullable();
            $table->string('arrest_location', 255)->nullable();
            $table->string('arrest_gps', 50)->nullable();

            $table->string('alleged_offence', 255);
            $table->string('statute_reference', 150)->nullable();

            $table->string('warrant_number', 100)->nullable();
            $table->string('warrant_issuing_court', 150)->nullable();
            $table->string('warrant_issuing_judge', 150)->nullable();
            $table->date('warrant_issue_date')->nullable();
            $table->date('warrant_expiry_date')->nullable();
            $table->string('warrant_document_path', 255)->nullable();

            $table->text('warrantless_justification')->nullable();
            $table->text('warrantless_witnesses')->nullable();

            $table->text('physical_condition')->nullable();
            $table->text('items_seized_preliminary')->nullable();
            $table->string('detention_location_after_arrest', 150)->nullable();

            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_case_arrests');
    }
};
