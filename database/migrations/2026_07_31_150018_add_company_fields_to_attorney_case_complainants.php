<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extra fields for the "Company" source-of-case entry form: a
     * company complainant is represented by a named individual and may
     * separately have retained a lawyer, distinct from the company's own
     * license number.
     */
    public function up(): void
    {
        Schema::table('attorney_case_complainants', function (Blueprint $table) {
            $table->string('representative_name', 150)->nullable()->after('full_name');
            $table->string('representative_title', 100)->nullable()->after('representative_name');
            $table->string('lawyer_name', 150)->nullable()->after('license_number');
            $table->string('lawyer_license_no', 50)->nullable()->after('lawyer_name');
        });
    }

    public function down(): void
    {
        Schema::table('attorney_case_complainants', function (Blueprint $table) {
            $table->dropColumn(['representative_name', 'representative_title', 'lawyer_name', 'lawyer_license_no']);
        });
    }
};
