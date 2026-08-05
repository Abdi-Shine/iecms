<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menu 3's Investigation Diaries — chronological log per case,
     * append-only (no update/delete route is ever registered for this
     * table). System entries are written directly by
     * CriminalCaseWorkflowController at each workflow milestone;
     * manual entries are added by investigators via
     * CriminalCaseController::storeDiaryEntry.
     */
    public function up(): void
    {
        Schema::create('criminal_case_diary_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('criminal_case_id')->constrained('criminal_cases')->cascadeOnDelete();
            $table->string('entry_type', 10); // system | manual
            $table->string('action_type', 100);
            $table->text('description')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_case_diary_entries');
    }
};
