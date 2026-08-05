<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menu 6's Detention Center registry — a richer, dedicated
     * detention-operations entity distinct from Stage 4's lightweight
     * criminal_case_custodies checkbox (which just flags whether a case
     * has an in-custody status). Linked to the case for arrestee
     * details rather than duplicating them.
     *
     * Access policy (confirmed): Institution Admin has full CRUD.
     * Investigators may only create a New Admission for their own
     * case's arrestee — enforced at the controller level, not via a
     * blanket 'create' grant on the CID Detention Center permission
     * module, since Investigators only hold 'view' on that module
     * (see 2026_08_05_000017_create_cid_roles.php).
     */
    public function up(): void
    {
        Schema::create('criminal_detainees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('criminal_case_id')->constrained('criminal_cases')->cascadeOnDelete();

            $table->string('detainee_name', 150);
            $table->dateTime('admission_datetime');
            $table->string('admitting_officer', 150);
            $table->string('cell_unit_reference', 100)->nullable();

            $table->string('custody_status', 30)->default('Newly Admitted');
            // Newly Admitted | Remanded | Awaiting Bail Hearing | Granted Bail | Released | Transferred | Deceased

            $table->date('legal_deadline')->nullable();
            $table->string('court_order_reference', 150)->nullable();

            $table->text('initial_health_declaration')->nullable();
            $table->boolean('property_receipt_signed')->default(false);
            $table->text('property_receipt_note')->nullable();
            $table->boolean('medical_screening_referred')->default(false);

            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_detainees');
    }
};
