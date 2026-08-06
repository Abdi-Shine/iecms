<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appeal_family_parties', function (Blueprint $table) {
            $table->id('PID');
            $table->unsignedBigInteger('family_case_id');
            $table->string('party_role', 50);
            $table->string('full_name', 150);
            $table->string('mother_name', 150)->nullable();
            $table->string('sex', 10)->nullable();
            $table->date('dob')->nullable();
            $table->string('contact_number', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('national_id', 50)->nullable();
            $table->string('passport_number', 50)->nullable();
            $table->string('passport_doc')->nullable();
            $table->string('power_of_attorney')->nullable();
            $table->string('biometric_scan')->nullable();
            $table->string('addedBy', 50)->nullable();
            $table->string('addedDate', 20)->nullable();
            $table->string('updatedBy', 50)->nullable();
            $table->string('updatedDate', 20)->nullable();
            $table->timestamps();

            $table->foreign('family_case_id')->references('AFCID')->on('appeal_family_registrations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appeal_family_parties');
    }
};
