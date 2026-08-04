<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $map = [
        'Individual'         => 'Shaqsi',
        'Lawyer'              => 'Shaqsi',
        'Company'             => 'Shirkad',
        'Government Agency'   => 'Dawladeed',
    ];

    public function up(): void
    {
        foreach ($this->map as $sourceOfCase => $caseType) {
            DB::table('attorney_cases')
                ->where('source_of_case', $sourceOfCase)
                ->whereNull('case_type')
                ->update(['case_type' => $caseType]);
        }
    }

    public function down(): void
    {
        foreach ($this->map as $sourceOfCase => $caseType) {
            DB::table('attorney_cases')
                ->where('source_of_case', $sourceOfCase)
                ->where('case_type', $caseType)
                ->update(['case_type' => null]);
        }
    }
};
