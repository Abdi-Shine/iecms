<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_evidence', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attorney_case_id');
            $table->string('evidence_type', 30)->default('Physical');
            $table->text('description');
            $table->string('collected_by', 100)->nullable();
            $table->date('collected_date')->nullable();
            $table->string('custody_holder', 100)->nullable();
            $table->string('storage_location', 150)->nullable();
            $table->string('file_path')->nullable();
            $table->string('status', 30)->default('In Custody');
            $table->timestamps();

            $table->foreign('attorney_case_id', 'ag_evidence_case_id_fk')
                ->references('ACID')->on('attorney_cases')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_evidence');
    }
};
