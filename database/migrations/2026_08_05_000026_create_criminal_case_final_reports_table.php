<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stage 5 — Final Investigation Report & AGO Submission. The
     * attorney_case_id link is the formal cross-institution referral the
     * spec's data-scoping rule requires ("no user sees data from...
     * unless a formal referral has been made"), replacing what would
     * otherwise be a loose text-match against attorney_cases
     * .reporting_agency (as AttorneyDashboardController currently does
     * for its "cidReferrals" stat). Deliberately not touching the
     * attorney_cases table itself — the link lives on CID's side only,
     * keeping this a one-way addition that can't break the Attorney
     * module.
     */
    public function up(): void
    {
        Schema::create('criminal_case_final_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('criminal_case_id')->constrained('criminal_cases')->cascadeOnDelete();

            $table->string('report_number', 50)->unique();
            $table->text('case_summary');
            $table->text('suspect_profile_summary')->nullable();
            $table->text('witness_summary')->nullable();
            $table->text('applicable_law')->nullable();
            $table->string('recommendation', 40);

            $table->foreignId('supervisor_endorsed_by')->nullable()
                ->constrained('users')->nullOnDelete()->name('cid_report_endorsed_by_fk');
            $table->timestamp('supervisor_endorsed_at')->nullable();

            $table->timestamp('submitted_to_ago_at')->nullable();
            $table->foreignId('ago_receiving_officer_id')->nullable()
                ->constrained('users')->nullOnDelete()->name('cid_report_ago_officer_fk');
            $table->foreignId('attorney_case_id')->nullable()
                ->constrained('attorney_cases', 'ACID')->nullOnDelete()->name('cid_report_attorney_case_fk');
            $table->string('ago_decision', 40)->nullable();

            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_case_final_reports');
    }
};
