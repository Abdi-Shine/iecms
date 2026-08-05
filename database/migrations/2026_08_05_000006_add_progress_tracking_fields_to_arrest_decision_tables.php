<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Arrest Decision: no status/reference field yet — add both.
        Schema::table('attorney_case_arrest_decisions', function (Blueprint $table) {
            $table->string('ob_reference')->nullable()->after('decision_date');
            $table->string('status')->default('Sugaya')->after('ob_reference');
            $table->text('approval_reason')->nullable()->after('status');
            $table->string('approved_by')->nullable()->after('approval_reason');
            $table->date('approved_date')->nullable()->after('approved_by');
        });

        // Arrest Without Warrant: already has ob_reference — add status/approval only.
        Schema::table('attorney_case_arrest_without_warrants', function (Blueprint $table) {
            $table->string('status')->default('Sugaya')->after('ob_reference');
            $table->text('approval_reason')->nullable()->after('status');
            $table->string('approved_by')->nullable()->after('approval_reason');
            $table->date('approved_date')->nullable()->after('approved_by');
        });

        // Warrant Of Arrest: warrant_status already serves as the approval status.
        Schema::table('attorney_case_warrant_of_arrests', function (Blueprint $table) {
            $table->string('ob_reference')->nullable()->after('warrant_number');
            $table->text('approval_reason')->nullable()->after('warrant_status');
            $table->string('approved_by')->nullable()->after('approval_reason');
            $table->date('approved_date')->nullable()->after('approved_by');
        });

        // Search And Seizure: warrant_status already serves as the approval status.
        Schema::table('attorney_case_search_and_seizures', function (Blueprint $table) {
            $table->string('ob_reference')->nullable()->after('warrant_number');
            $table->text('approval_reason')->nullable()->after('warrant_status');
            $table->string('approved_by')->nullable()->after('approval_reason');
            $table->date('approved_date')->nullable()->after('approved_by');
        });

        // Asset Recovery: seizure_status already serves as the approval status.
        Schema::table('attorney_case_asset_recoveries', function (Blueprint $table) {
            $table->string('ob_reference')->nullable()->after('court_order_reference');
            $table->text('approval_reason')->nullable()->after('seizure_status');
            $table->string('approved_by')->nullable()->after('approval_reason');
            $table->date('approved_date')->nullable()->after('approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('attorney_case_arrest_decisions', function (Blueprint $table) {
            $table->dropColumn(['ob_reference', 'status', 'approval_reason', 'approved_by', 'approved_date']);
        });

        Schema::table('attorney_case_arrest_without_warrants', function (Blueprint $table) {
            $table->dropColumn(['status', 'approval_reason', 'approved_by', 'approved_date']);
        });

        Schema::table('attorney_case_warrant_of_arrests', function (Blueprint $table) {
            $table->dropColumn(['ob_reference', 'approval_reason', 'approved_by', 'approved_date']);
        });

        Schema::table('attorney_case_search_and_seizures', function (Blueprint $table) {
            $table->dropColumn(['ob_reference', 'approval_reason', 'approved_by', 'approved_date']);
        });

        Schema::table('attorney_case_asset_recoveries', function (Blueprint $table) {
            $table->dropColumn(['ob_reference', 'approval_reason', 'approved_by', 'approved_date']);
        });
    }
};
