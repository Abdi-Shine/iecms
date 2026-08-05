<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menu 4's Biometrics sub-menu. Per spec, DNA is stored as a
     * reference number only, never raw genetic data — same treatment
     * applied to fingerprint/iris here (reference numbers pointing to
     * wherever the physical/external record lives, not templates).
     * There is no national biometric database to integrate with in
     * this codebase, so match_status/match_reference are manually
     * entered by staff rather than populated by a real lookup.
     */
    public function up(): void
    {
        Schema::create('criminal_case_biometrics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('criminal_case_id')->constrained('criminal_cases')->cascadeOnDelete();

            $table->string('person_name', 150);
            $table->string('person_role', 30); // suspect | person_of_interest

            $table->string('fingerprint_reference', 100)->nullable();
            $table->string('facial_photo_path', 255)->nullable();
            $table->string('dna_reference', 100)->nullable();
            $table->string('iris_scan_reference', 100)->nullable();

            $table->string('match_status', 20)->default('Not Checked'); // Match Found | No Match | Inconclusive | Not Checked
            $table->string('match_reference', 150)->nullable();

            $table->string('captured_by', 150);
            $table->date('captured_date');

            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_case_biometrics');
    }
};
