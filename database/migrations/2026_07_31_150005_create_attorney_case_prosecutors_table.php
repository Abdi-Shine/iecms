<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_case_prosecutors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attorney_case_id');
            $table->unsignedBigInteger('prosecutor_id');
            $table->string('role', 30)->default('Lead');
            $table->date('assigned_date')->nullable();
            $table->timestamps();

            $table->foreign('attorney_case_id', 'ag_case_prosecutors_case_id_fk')
                ->references('ACID')->on('attorney_cases')->onDelete('cascade');
            $table->foreign('prosecutor_id', 'ag_case_prosecutors_prosecutor_id_fk')
                ->references('AID')->on('employees')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_prosecutors');
    }
};
