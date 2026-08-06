<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appeal_family_legal_representatives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('family_case_id');
            $table->unsignedBigInteger('party_id')->nullable();
            $table->string('party_role', 50)->nullable();
            $table->string('rep_name', 150);
            $table->string('rep_doc_number', 100)->nullable();
            $table->string('rep_doc', 255)->nullable();
            $table->string('addedBy', 150)->nullable();
            $table->date('addedDate')->nullable();
            $table->timestamps();

            $table->foreign('family_case_id')->references('AFCID')->on('appeal_family_registrations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appeal_family_legal_representatives');
    }
};
