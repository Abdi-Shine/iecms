<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Schema::dropIfExists('district_civil_lawyers');
        Schema::dropIfExists('lawyers');

        Schema::create('lawyers', function (Blueprint $table) {
            $table->bigIncrements('LRID');
            $table->string('Lcode', 50)->nullable();
            $table->string('LID', 50)->nullable();
            $table->string('LawyerName', 100);
            $table->string('Phone', 25)->nullable();
            $table->string('Email', 50)->nullable();
            $table->string('Gender', 50)->default('Male');
            $table->string('DOB', 50)->nullable();
            $table->string('POB', 50)->nullable();
            $table->string('photo', 100)->nullable();
            $table->string('CourtID', 50)->nullable();
            $table->string('Position', 50)->nullable();
            $table->string('Grade', 100)->nullable();
            $table->string('status', 25)->default('active');
            $table->string('addedBy', 50)->nullable();
            $table->string('addedDate', 25)->nullable();
            $table->string('updatedBy', 50)->nullable();
            $table->string('updatedDate', 50)->nullable();
        });

        Schema::create('district_civil_lawyers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('civil_case_id');
            $table->unsignedBigInteger('lawyer_id');
            $table->unsignedBigInteger('party_id')->nullable();
            $table->string('party_role')->nullable();
            $table->string('lawyer_type')->nullable();
            $table->date('assignment_date')->nullable();
            $table->text('reason')->nullable();
            $table->string('status')->default('Active');
            $table->string('addedBy')->nullable();
            $table->timestamp('addedDate')->nullable();
            $table->timestamps();

            $table->foreign('civil_case_id')->references('CRID')->on('distric_civil_registrations')->onDelete('cascade');
            $table->foreign('lawyer_id')->references('LRID')->on('lawyers')->onDelete('cascade');
            $table->foreign('party_id')->references('PID')->on('district_civil_parties')->onDelete('set null');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::dropIfExists('district_civil_lawyers');
        Schema::dropIfExists('lawyers');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
