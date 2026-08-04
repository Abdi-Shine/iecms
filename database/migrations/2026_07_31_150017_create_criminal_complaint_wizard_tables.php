<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Backing tables + columns for the 9-step Criminal Complaint Report Form
     * (Complainant / Victims / Accused / Incident / Crime / Evidence /
     * Witnesses / Relief / Declaration). attorney_case_parties stays for the
     * older generic party model still used elsewhere; these new tables are
     * purpose-specific to the wizard's richer per-role fields.
     */
    public function up(): void
    {
        Schema::table('attorney_cases', function (Blueprint $table) {
            $table->string('source_of_case', 50)->nullable()->after('reporting_agency');
            $table->date('incident_date')->nullable()->after('date_reported');
            $table->string('incident_time', 20)->nullable()->after('incident_date');
            $table->string('incident_location', 255)->nullable()->after('incident_time');
            $table->text('relief_sought')->nullable()->after('summary');
            $table->boolean('declaration_accepted')->default(false)->after('relief_sought');
            $table->string('declaration_signed_by', 150)->nullable()->after('declaration_accepted');
            $table->date('declaration_date')->nullable()->after('declaration_signed_by');
        });

        Schema::create('attorney_case_complainants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attorney_case_id');
            $table->string('type', 20)->default('Individual');
            $table->string('full_name', 150);
            $table->string('phone_number', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('occupation', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('license_number', 50)->nullable();
            $table->timestamps();

            $table->foreign('attorney_case_id', 'ag_complainants_case_id_fk')
                ->references('ACID')->on('attorney_cases')->onDelete('cascade');
        });

        Schema::create('attorney_case_victims', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attorney_case_id');
            $table->string('full_name', 150);
            $table->string('phone_number', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('gender', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('relationship_to_accused', 100)->nullable();
            $table->text('impact_description')->nullable();
            $table->timestamps();

            $table->foreign('attorney_case_id', 'ag_victims_case_id_fk')
                ->references('ACID')->on('attorney_cases')->onDelete('cascade');
        });

        Schema::create('attorney_case_accused', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attorney_case_id');
            $table->string('full_name', 150);
            $table->string('alias', 150)->nullable();
            $table->string('phone_number', 30)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('id_number', 50)->nullable();
            $table->timestamps();

            $table->foreign('attorney_case_id', 'ag_accused_case_id_fk')
                ->references('ACID')->on('attorney_cases')->onDelete('cascade');
        });

        Schema::create('attorney_case_evidence_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attorney_case_id');
            $table->string('evidence_type', 50)->default('Document');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->date('collected_date')->nullable();
            $table->timestamps();

            $table->foreign('attorney_case_id', 'ag_evidence_items_case_id_fk')
                ->references('ACID')->on('attorney_cases')->onDelete('cascade');
        });

        Schema::create('attorney_case_witnesses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attorney_case_id');
            $table->string('full_name', 150);
            $table->string('phone_number', 30)->nullable();
            $table->text('address')->nullable();
            $table->text('statement')->nullable();
            $table->timestamps();

            $table->foreign('attorney_case_id', 'ag_witnesses_case_id_fk')
                ->references('ACID')->on('attorney_cases')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_witnesses');
        Schema::dropIfExists('attorney_case_evidence_items');
        Schema::dropIfExists('attorney_case_accused');
        Schema::dropIfExists('attorney_case_victims');
        Schema::dropIfExists('attorney_case_complainants');

        Schema::table('attorney_cases', function (Blueprint $table) {
            $table->dropColumn([
                'source_of_case', 'incident_date', 'incident_time', 'incident_location',
                'relief_sought', 'declaration_accepted', 'declaration_signed_by', 'declaration_date',
            ]);
        });
    }
};
