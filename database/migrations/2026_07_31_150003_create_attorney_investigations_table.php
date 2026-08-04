<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_investigations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attorney_case_id');
            $table->unsignedBigInteger('investigator_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 30)->default('Ongoing');
            $table->text('findings_summary')->nullable();
            $table->string('report_file')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->timestamps();

            $table->foreign('attorney_case_id')->references('ACID')->on('attorney_cases')->onDelete('cascade');
            $table->foreign('investigator_id')->references('AID')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_investigations');
    }
};
