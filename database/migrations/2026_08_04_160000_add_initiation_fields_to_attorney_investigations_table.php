<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attorney_investigations', function (Blueprint $table) {
            $table->string('specific_offence', 255)->nullable()->after('status');
            $table->string('victim_type', 20)->nullable()->after('specific_offence');
            $table->string('prosecutor_name', 150)->nullable()->after('report_file');
            $table->string('prosecutor_department', 150)->nullable()->after('prosecutor_name');
            $table->string('prosecutor_signature', 150)->nullable()->after('prosecutor_department');
            $table->date('signature_date')->nullable()->after('prosecutor_signature');
            $table->string('initial_evidence_file')->nullable()->after('signature_date');
            $table->string('supporting_documents_file')->nullable()->after('initial_evidence_file');
        });

        // The mockup's "Approximate Time of Offence" example text ("Between 2-4pm")
        // no longer fits the original short time-picker column (varchar 20).
        Schema::table('attorney_cases', function (Blueprint $table) {
            $table->string('incident_time', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('attorney_investigations', function (Blueprint $table) {
            $table->dropColumn([
                'specific_offence', 'victim_type', 'prosecutor_name', 'prosecutor_department',
                'prosecutor_signature', 'signature_date', 'initial_evidence_file', 'supporting_documents_file',
            ]);
        });

        Schema::table('attorney_cases', function (Blueprint $table) {
            $table->string('incident_time', 20)->nullable()->change();
        });
    }
};
