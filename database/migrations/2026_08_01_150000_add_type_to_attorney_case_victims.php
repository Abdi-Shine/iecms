<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Victim type ('Shakhsi' individual vs 'Qaranka' state/institution) drives
     * which fields the intake wizard shows and requires for a given victim row.
     */
    public function up(): void
    {
        Schema::table('attorney_case_victims', function (Blueprint $table) {
            $table->string('type', 20)->nullable()->after('full_name');
        });
    }

    public function down(): void
    {
        Schema::table('attorney_case_victims', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
