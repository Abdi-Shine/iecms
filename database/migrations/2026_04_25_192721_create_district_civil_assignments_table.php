<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('district_civil_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('civil_case_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('panel_role')->nullable();
            $table->string('assigned_by')->nullable();
            $table->date('assignment_date')->nullable();
            $table->string('status')->default('Assigned'); // Assigned, Reassigned, Completed
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('civil_case_id')->references('CRID')->on('distric_civil_registrations')->onDelete('cascade');
            $table->foreign('employee_id')->references('AID')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('civil_case_assignments');
    }
};
