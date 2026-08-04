<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attorney_case_investigation_decisions', function (Blueprint $table) {
            // CID Investigation Summary
            $table->text('investigation_summary')->nullable()->after('attorney_case_id');

            // Evidence Assessment
            $table->string('evidence_quality')->nullable()->after('investigation_summary');
            $table->string('evidence_completeness')->nullable()->after('evidence_quality');
            $table->text('evidence_assessment_notes')->nullable()->after('evidence_completeness');

            // Witness Interviews
            $table->string('witnesses_interviewed')->nullable()->after('evidence_assessment_notes');
            $table->text('witness_interview_notes')->nullable()->after('witnesses_interviewed');

            // Legal Assessment
            $table->string('legal_sufficiency')->nullable()->after('witness_interview_notes');
            $table->string('legal_basis_identified')->nullable()->after('legal_sufficiency');
            $table->text('legal_assessment_notes')->nullable()->after('legal_basis_identified');

            // Investigation Decision (extends the existing decision/reasoning/decision_date)
            $table->text('next_steps')->nullable()->after('decision_date');

            // Resource Requirements
            $table->boolean('additional_investigation_needed')->default(false)->after('next_steps');
            $table->string('estimated_completion_time')->nullable()->after('additional_investigation_needed');
            $table->text('resource_requirements')->nullable()->after('estimated_completion_time');

            // Risk Assessment
            $table->string('overall_risk_level')->nullable()->after('resource_requirements');
            $table->json('risk_factors')->nullable()->after('overall_risk_level');
            $table->text('risk_mitigation_strategies')->nullable()->after('risk_factors');

            // Supporting Documentation
            $table->string('cid_investigation_report_path')->nullable()->after('risk_mitigation_strategies');
            $table->string('evidence_photographs_path')->nullable()->after('cid_investigation_report_path');
            $table->string('witness_statements_path')->nullable()->after('evidence_photographs_path');
            $table->string('other_documents_path')->nullable()->after('witness_statements_path');

            // Approval
            $table->string('recommended_by')->nullable()->after('other_documents_path');
            $table->date('recommended_date')->nullable()->after('recommended_by');
            $table->string('approved_by')->nullable()->after('recommended_date');
            $table->date('approved_date')->nullable()->after('approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('attorney_case_investigation_decisions', function (Blueprint $table) {
            $table->dropColumn([
                'investigation_summary',
                'evidence_quality',
                'evidence_completeness',
                'evidence_assessment_notes',
                'witnesses_interviewed',
                'witness_interview_notes',
                'legal_sufficiency',
                'legal_basis_identified',
                'legal_assessment_notes',
                'next_steps',
                'additional_investigation_needed',
                'estimated_completion_time',
                'resource_requirements',
                'overall_risk_level',
                'risk_factors',
                'risk_mitigation_strategies',
                'cid_investigation_report_path',
                'evidence_photographs_path',
                'witness_statements_path',
                'other_documents_path',
                'recommended_by',
                'recommended_date',
                'approved_by',
                'approved_date',
            ]);
        });
    }
};
