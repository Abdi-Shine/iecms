<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Real configuration consulted by CriminalCase::nextCaseNumber(),
     * CriminalCaseOccurrenceBook::nextObNumber(), and
     * CriminalCaseFinalReport::nextReportNumber() — not a decorative
     * settings page. locked flips to true automatically the first time
     * a format is used to generate a real number, since changing the
     * prefix/sequence mid-run would break uniqueness/ordering
     * assumptions on already-issued numbers.
     */
    public function up(): void
    {
        Schema::create('criminal_number_formats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();

            $table->string('format_key', 30);
            // case_number | ob_number | report_number | evidence_id | warrant_number | detainee_id

            $table->string('prefix', 30);
            $table->boolean('include_year')->default(true);
            $table->unsignedTinyInteger('year_digits')->default(4);
            $table->unsignedTinyInteger('sequence_length')->default(5);
            $table->string('reset_period', 10)->default('yearly'); // yearly | never
            $table->boolean('locked')->default(false);

            $table->string('added_by', 100)->nullable();
            $table->timestamps();

            $table->unique(['institution_id', 'format_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_number_formats');
    }
};
