<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menu 4's Investigation Reports — Progress/Interim/Expert-Forensic
     * report types, distinct from the Final Investigation Report
     * (criminal_case_final_reports, Stage 5). "Submitted" here means
     * finalized/distributed within the department, not AGO submission
     * — that remains Stage 5's job specifically.
     */
    public function up(): void
    {
        Schema::create('criminal_case_investigation_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('criminal_case_id')->constrained('criminal_cases')->cascadeOnDelete();

            $table->string('report_type', 30); // Progress Report | Interim Report | Expert-Forensic Report
            $table->text('content');
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete()
                ->name('cid_investigation_report_author_fk');

            $table->string('status', 20)->default('Draft'); // Draft | Supervisor Review | Approved | Submitted
            $table->foreignId('reviewed_by')->nullable()
                ->constrained('users')->nullOnDelete()->name('cid_investigation_report_reviewer_fk');
            $table->timestamp('reviewed_at')->nullable();

            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_case_investigation_reports');
    }
};
