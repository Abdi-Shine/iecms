<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * offense_type now holds one or more selected sub-case names joined
     * by ", " (multi-select of the Ciqaabta sub-case list), which can
     * easily exceed the old varchar(100) — several sub-case names alone
     * are 100+ characters.
     */
    public function up(): void
    {
        Schema::table('attorney_cases', function (Blueprint $table) {
            $table->text('offense_type')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('attorney_cases', function (Blueprint $table) {
            $table->string('offense_type', 100)->nullable(false)->change();
        });
    }
};
