<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_proceedings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attorney_case_id');
            $table->unsignedInteger('court_id')->nullable();
            $table->unsignedBigInteger('judge_id')->nullable();
            $table->date('hearing_date');
            $table->string('hearing_time', 20)->nullable();
            $table->string('proceeding_type', 50)->default('Arraignment');
            $table->text('outcome')->nullable();
            $table->string('status', 30)->default('Scheduled');
            $table->text('notes')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->timestamps();

            $table->foreign('attorney_case_id', 'ag_proceedings_case_id_fk')
                ->references('ACID')->on('attorney_cases')->onDelete('cascade');
            $table->foreign('court_id')->references('CAI')->on('courts')->nullOnDelete();
            $table->foreign('judge_id', 'ag_proceedings_judge_id_fk')
                ->references('AID')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_proceedings');
    }
};
