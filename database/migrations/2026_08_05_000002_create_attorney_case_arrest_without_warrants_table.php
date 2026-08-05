<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_case_arrest_without_warrants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attorney_case_id')->constrained('attorney_cases', 'ACID')->cascadeOnDelete();

            $table->string('suspect_name')->nullable();
            $table->date('arrest_date');
            $table->time('arrest_time')->nullable();
            $table->string('arrest_location')->nullable();
            $table->string('arresting_officer_name')->nullable();
            $table->string('arresting_officer_rank')->nullable();
            $table->string('grounds_for_warrantless_arrest')->nullable();
            $table->text('circumstances')->nullable();
            $table->boolean('force_used')->default(false);
            $table->text('force_description')->nullable();
            $table->boolean('rights_informed')->default(false);
            $table->string('witnesses_present')->nullable();
            $table->string('ob_reference')->nullable();
            $table->string('reporting_officer')->nullable();
            $table->date('report_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_arrest_without_warrants');
    }
};
