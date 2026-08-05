<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Stage 4-A — custody management. One record per case; custody_status
     * changes are tracked via plain updates (not a history table) since
     * the spec only asks for current status plus review dates, not a
     * full custody-transition audit trail like evidence's chain of
     * custody. The Auditable trait still captures every change.
     */
    public function up(): void
    {
        Schema::create('criminal_case_custodies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('criminal_case_id')->constrained('criminal_cases')->cascadeOnDelete();

            $table->string('custody_status', 30)->default('In Custody');
            $table->string('custody_location', 150)->nullable();
            $table->string('cell_unit_reference', 100)->nullable();

            $table->date('custody_start_date');
            $table->date('legal_deadline')->nullable();

            $table->text('bail_conditions')->nullable();

            $table->date('custody_review_date')->nullable();
            $table->string('custody_review_officer', 150)->nullable();

            $table->string('medical_check_status', 50)->nullable();
            $table->text('welfare_notes')->nullable();

            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_case_custodies');
    }
};
