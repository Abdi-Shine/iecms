<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Renames the courts table to judiciaries, continuing the earlier
     * Court -> "Judiciary Registration" UI rename. Placed after every
     * existing migration that creates or foreign-keys against `courts`
     * (payments, tariffs, district_*_payments, attorney_proceedings, and
     * the 2026_08_06 court-type backfill), so `migrate:fresh` still works:
     * those migrations run first and only see the table under its old
     * name, exactly as they did historically. MySQL updates the InnoDB
     * foreign key metadata automatically on rename.
     */
    public function up(): void
    {
        Schema::rename('courts', 'judiciaries');
    }

    public function down(): void
    {
        Schema::rename('judiciaries', 'courts');
    }
};
