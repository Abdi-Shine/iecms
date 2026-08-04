<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_case_investigation_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attorney_case_id')->constrained('attorney_cases', 'ACID')->cascadeOnDelete();
            $table->string('decision');
            $table->text('reasoning')->nullable();
            $table->string('decided_by')->nullable();
            $table->date('decision_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_investigation_decisions');
    }
};
