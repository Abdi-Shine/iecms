<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('district_execution_legal_representatives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('execution_case_id');
            $table->unsignedBigInteger('party_id')->nullable();
            $table->string('party_role', 50)->nullable();
            $table->string('rep_name', 150);
            $table->string('rep_doc_number', 100)->nullable();
            $table->string('rep_doc', 255)->nullable();
            $table->string('addedBy', 150)->nullable();
            $table->date('addedDate')->nullable();
            $table->timestamps();

            $table->foreign('execution_case_id', 'dexec_legal_reps_case_id_fk')->references('ECID')->on('district_execution_registrations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('district_execution_legal_representatives');
    }
};
