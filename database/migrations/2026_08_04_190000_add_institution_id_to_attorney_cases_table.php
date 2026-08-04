<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attorney_cases', function (Blueprint $table) {
            $table->foreignId('institution_id')->nullable()->after('ACID')
                ->constrained('institutions')->nullOnDelete();
        });

        $agoId = DB::table('institutions')->where('type', 'ago')->value('id');

        if ($agoId) {
            DB::table('attorney_cases')->update(['institution_id' => $agoId]);
        }
    }

    public function down(): void
    {
        Schema::table('attorney_cases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institution_id');
        });
    }
};
