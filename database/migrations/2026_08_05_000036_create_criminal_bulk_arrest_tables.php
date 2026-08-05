<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bulk Arrest Management — one operation (raid, mass arrest, public
     * order event) with multiple arrestees. Each member optionally
     * generates its own individual CriminalCase + arrest + OB record
     * (via CriminalBulkArrestController::generateCase), keeping the
     * per-arrestee data in the same tables Stage 1/2 already use rather
     * than duplicating arrest fields here.
     */
    public function up(): void
    {
        Schema::create('criminal_bulk_arrest_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();

            $table->string('event_name', 150);
            $table->date('event_date');
            $table->string('location', 255)->nullable();
            $table->string('operation_reference', 100)->nullable();
            $table->string('commanding_officer', 150);

            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('criminal_bulk_arrest_members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('bulk_arrest_event_id')->constrained('criminal_bulk_arrest_events')
                ->cascadeOnDelete()->name('cid_bulk_member_event_fk');

            $table->string('arrestee_name', 150);
            $table->string('alleged_offence', 255);
            $table->foreignId('assigned_investigator_id')->nullable()
                ->constrained('users')->nullOnDelete()->name('cid_bulk_member_investigator_fk');
            $table->foreignId('criminal_case_id')->nullable()
                ->constrained('criminal_cases')->nullOnDelete()->name('cid_bulk_member_case_fk');

            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_bulk_arrest_members');
        Schema::dropIfExists('criminal_bulk_arrest_events');
    }
};
