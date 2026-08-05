<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Covers five of Menu 5's sub-menus via a request_type discriminator
     * rather than five near-identical tables — they share one lifecycle
     * (request -> issued/ratified -> executed -> expired/cancelled):
     * Arrest Without Warrant (AGO) ratification, Warrant of Arrest
     * (AGO), Search & Seizure (AGO), Asset Recovery (AGO), and Search
     * Warrants (direct to courts, no AGO routing). The four *_ago types
     * create a formally linked attorney_case_id referral on approval,
     * same pattern as criminal_case_final_reports in Stage 5. Execution
     * fields (execution_outcome/execution_date/items_seized) double as
     * Menu 5's separate "Search & Seizure" operational registry for the
     * search-type rows, rather than a sixth table.
     */
    public function up(): void
    {
        Schema::create('criminal_legal_process_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('criminal_case_id')->constrained('criminal_cases')->cascadeOnDelete();

            $table->string('request_type', 40);
            // warrant_of_arrest_ago | search_seizure_ago | asset_recovery_ago |
            // arrest_without_warrant_ago | search_warrant_court

            $table->string('requesting_officer', 150);
            $table->string('urgency_level', 20)->nullable();
            $table->text('grounds')->nullable();
            $table->text('details')->nullable(); // items sought / asset description / circumstances

            $table->string('status', 20)->default('Requested');
            $table->string('issuing_authority', 150)->nullable();
            $table->string('document_path', 255)->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();

            $table->text('execution_outcome')->nullable();
            $table->date('execution_date')->nullable();
            $table->text('items_seized')->nullable();
            $table->decimal('estimated_value', 14, 2)->nullable();

            $table->foreignId('attorney_case_id')->nullable()
                ->constrained('attorney_cases', 'ACID')->nullOnDelete()->name('cid_legalreq_attorney_case_fk');

            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_legal_process_requests');
    }
};
