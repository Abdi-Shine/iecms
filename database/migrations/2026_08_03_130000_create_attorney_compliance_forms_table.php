<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_compliance_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attorney_case_id')->constrained('attorney_cases', 'ACID')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees', 'AID')->cascadeOnDelete();
            $table->string('form_type');
            $table->string('form_code');
            $table->text('notes')->nullable();
            $table->string('signature');
            $table->date('signed_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_compliance_forms');
    }
};
