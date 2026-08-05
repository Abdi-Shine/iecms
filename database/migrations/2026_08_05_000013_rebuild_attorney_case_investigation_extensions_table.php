<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attorney_case_investigation_extensions', function (Blueprint $table) {
            $table->dropColumn([
                'current_deadline',
                'requested_days',
                'new_deadline',
                'justification',
                'requesting_prosecutor',
                'request_date',
                'supporting_document_path',
            ]);
        });

        Schema::table('attorney_case_investigation_extensions', function (Blueprint $table) {
            // General information about the case
            $table->date('date_registered_investigation')->nullable()->after('attorney_case_id');
            $table->string('incident_type')->nullable()->after('date_registered_investigation');
            $table->text('legal_articles')->nullable()->after('incident_type');
            $table->date('date_offence_occurred')->nullable()->after('legal_articles');
            $table->string('offence_location')->nullable()->after('date_offence_occurred');
            $table->date('date_investigation_commenced')->nullable()->after('offence_location');
            $table->string('court_name')->nullable()->after('date_investigation_commenced');
            $table->string('court_case_reference')->nullable()->after('court_name');

            // Reason for extension request
            $table->boolean('reason_ongoing_investigation')->default(false)->after('court_case_reference');
            $table->boolean('reason_awaiting_scan_results')->default(false)->after('reason_ongoing_investigation');
            $table->boolean('reason_awaiting_institutional_experts')->default(false)->after('reason_awaiting_scan_results');
            $table->boolean('reason_awaiting_witness_statements')->default(false)->after('reason_awaiting_institutional_experts');
            $table->boolean('reason_other')->default(false)->after('reason_awaiting_witness_statements');
            $table->text('reason_other_specify')->nullable()->after('reason_other');

            // Requested extension period
            $table->string('extension_period')->nullable()->after('reason_other_specify');
            $table->string('extension_period_other')->nullable()->after('extension_period');

            // Signature
            $table->string('prosecutor_name')->nullable()->after('extension_period_other');
            $table->string('prosecutor_title')->nullable()->after('prosecutor_name');
        });
    }

    public function down(): void
    {
        Schema::table('attorney_case_investigation_extensions', function (Blueprint $table) {
            $table->dropColumn([
                'date_registered_investigation',
                'incident_type',
                'legal_articles',
                'date_offence_occurred',
                'offence_location',
                'date_investigation_commenced',
                'court_name',
                'court_case_reference',
                'reason_ongoing_investigation',
                'reason_awaiting_scan_results',
                'reason_awaiting_institutional_experts',
                'reason_awaiting_witness_statements',
                'reason_other',
                'reason_other_specify',
                'extension_period',
                'extension_period_other',
                'prosecutor_name',
                'prosecutor_title',
            ]);
        });

        Schema::table('attorney_case_investigation_extensions', function (Blueprint $table) {
            $table->date('current_deadline')->nullable();
            $table->string('requested_days')->nullable();
            $table->date('new_deadline')->nullable();
            $table->text('justification')->nullable();
            $table->string('requesting_prosecutor')->nullable();
            $table->date('request_date')->nullable();
            $table->string('supporting_document_path')->nullable();
        });
    }
};
