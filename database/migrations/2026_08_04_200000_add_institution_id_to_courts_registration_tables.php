<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Root tables for the Courts module's 5 case-type registries. Each has
     * its own primary key name (CRID/FCID/CMID/ECID/ACID) but the same
     * shape otherwise — child records (parties, hearings, judgments, etc.)
     * inherit isolation transitively through their FK to these.
     */
    private array $tables = [
        'distric_civil_registrations',
        'district_family_registrations',
        'district_criminal_registrations',
        'district_execution_registrations',
        'appeal_civil_registrations',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->foreignId('institution_id')->nullable()
                    ->constrained('institutions')->nullOnDelete();
            });
        }

        $courtsId = DB::table('institutions')->where('type', 'court')->value('id');

        if ($courtsId) {
            foreach ($this->tables as $table) {
                DB::table($table)->update(['institution_id' => $courtsId]);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropConstrainedForeignId('institution_id');
            });
        }
    }
};
