<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stage 3-B — evidence items. Tamper-evident per the CID spec
     * (Menu 4's Evidence sub-menu: "records cannot be deleted, only
     * updated with tracked changes") — no destroy route is registered
     * for this table, only status-transition updates, and every change
     * is captured by the Auditable trait on CriminalCaseEvidenceItem.
     * Deliberately deeper than Attorney's evidence table (which only
     * tracks current holder): real transfer history lives in
     * criminal_case_evidence_custody_logs.
     */
    public function up(): void
    {
        Schema::create('criminal_case_evidence_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('criminal_case_id')->constrained('criminal_cases')->cascadeOnDelete();
            $table->string('description', 255);
            $table->string('evidence_type', 20); // physical | digital | documentary | biological
            $table->date('collection_date');
            $table->string('collection_location', 255)->nullable();
            $table->string('collected_by', 150);
            $table->string('storage_location', 150)->nullable();
            $table->string('status', 30)->default('collected');
            $table->string('file_path', 255)->nullable();
            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_case_evidence_items');
    }
};
