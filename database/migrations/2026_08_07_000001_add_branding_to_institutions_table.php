<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mirrors the logo/stamp/letterhead branding columns already used on
     * the courts table, so institutions (CID, AGO, Corrections, ...) can
     * carry their own logo, letterhead ("header"), and official stamp for
     * use in their sidebar and generated documents.
     */
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->string('logo', 100)->nullable()->after('type');
            $table->string('stamp', 150)->nullable()->after('logo');
            $table->string('letterhead')->nullable()->after('stamp');
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn(['logo', 'stamp', 'letterhead']);
        });
    }
};
