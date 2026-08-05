<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_case_asset_recoveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attorney_case_id')->constrained('attorney_cases', 'ACID')->cascadeOnDelete();

            $table->text('asset_description')->nullable();
            $table->string('estimated_value')->nullable();
            $table->string('asset_location')->nullable();
            $table->text('ownership_details')->nullable();
            $table->text('legal_basis_for_seizure')->nullable();
            $table->date('application_date');
            $table->string('requesting_officer')->nullable();
            $table->string('court_order_reference')->nullable();
            $table->string('seizure_status')->default('Sugaya');
            $table->date('seizure_date')->nullable();
            $table->string('custody_location')->nullable();
            $table->text('disposition_notes')->nullable();
            $table->string('supporting_document_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_asset_recoveries');
    }
};
