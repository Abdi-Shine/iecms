<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Distinguishes Menu 3's "Internal OB" sub-menu (station-level
     * incidents, staff incidents — not from public/external complaints)
     * from the regular Occurrence Books registry.
     */
    public function up(): void
    {
        Schema::table('criminal_case_occurrence_books', function (Blueprint $table) {
            $table->boolean('is_internal')->default(false)->after('ob_number');
        });
    }

    public function down(): void
    {
        Schema::table('criminal_case_occurrence_books', function (Blueprint $table) {
            $table->dropColumn('is_internal');
        });
    }
};
