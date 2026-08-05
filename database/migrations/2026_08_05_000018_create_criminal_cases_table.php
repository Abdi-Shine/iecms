<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The CID investigation case — mirrors attorney_cases' shape
     * (see 2026_07_31_150001_create_attorney_cases_table.php) but with a
     * plain auto-increment id rather than a custom PK, and a
     * current_stage column since the CID spec requires hard sequential
     * stage gating (unlike Attorney's workflow, which only tracks
     * completion loosely via relation existence).
     */
    public function up(): void
    {
        Schema::create('criminal_cases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('case_number', 50)->unique();
            $table->string('current_stage', 40)->default('arrest');
            $table->string('status', 30)->default('Open');
            $table->string('priority', 20)->default('Routine');
            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_cases');
    }
};
