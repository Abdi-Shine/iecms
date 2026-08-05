<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_case_arrest_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attorney_case_id')->constrained('attorney_cases', 'ACID')->cascadeOnDelete();

            $table->string('suspect_name')->nullable();
            $table->string('decision'); // Loo Baahan Yahay Xidhitaan / Looma Baahna Xidhitaan
            $table->text('legal_grounds')->nullable();
            $table->string('urgency_level')->nullable();
            $table->boolean('flight_risk')->default(false);
            $table->boolean('public_safety_risk')->default(false);
            $table->text('reasoning')->nullable();
            $table->string('next_action')->nullable(); // which follow-up form applies
            $table->string('decided_by')->nullable();
            $table->date('decision_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_arrest_decisions');
    }
};
