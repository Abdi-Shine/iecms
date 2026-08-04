<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $map = [
        'Individual'         => 'Shakhsi',
        'Lawyer'              => 'Qareen',
        'Company'             => 'Shirkad',
        'Government Agency'   => "Hay'ad Dawladeed",
    ];

    public function up(): void
    {
        foreach ($this->map as $english => $somali) {
            DB::table('attorney_case_complainants')
                ->where('type', $english)
                ->update(['type' => $somali]);
        }

        Schema::table('attorney_case_complainants', function (Blueprint $table) {
            $table->string('type', 20)->default('Shakhsi')->change();
        });
    }

    public function down(): void
    {
        Schema::table('attorney_case_complainants', function (Blueprint $table) {
            $table->string('type', 20)->default('Individual')->change();
        });

        foreach ($this->map as $english => $somali) {
            DB::table('attorney_case_complainants')
                ->where('type', $somali)
                ->update(['type' => $english]);
        }
    }
};
