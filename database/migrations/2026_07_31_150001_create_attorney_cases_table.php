<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_cases', function (Blueprint $table) {
            $table->bigIncrements('ACID');
            $table->string('case_number', 50)->unique();
            $table->string('title', 255);
            $table->string('offense_type', 100);
            $table->date('date_reported');
            $table->string('reporting_agency', 150)->nullable();
            $table->string('status', 50)->default('Reported');
            $table->string('priority', 20)->default('Medium');
            $table->text('summary')->nullable();
            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_cases');
    }
};
