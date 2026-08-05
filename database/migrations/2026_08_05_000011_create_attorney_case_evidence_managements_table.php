<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_case_evidence_managements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attorney_case_id')->constrained('attorney_cases', 'ACID')->cascadeOnDelete();

            $table->text('evidence_description')->nullable();
            $table->string('evidence_type')->nullable();
            $table->date('date_collected');
            $table->string('collected_by')->nullable();
            $table->string('storage_location')->nullable();
            $table->string('condition')->nullable();
            $table->text('chain_of_custody_notes')->nullable();
            $table->boolean('catalogued')->default(false);
            $table->string('evidence_file_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_evidence_managements');
    }
};
