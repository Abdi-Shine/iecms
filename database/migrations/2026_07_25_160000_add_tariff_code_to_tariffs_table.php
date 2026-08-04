<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Tariifa ID" in the create/edit form is a manually-entered reference
     * code (e.g. matching a paper tariff schedule), separate from the
     * auto-generated database ID used internally.
     */
    public function up(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $table->string('tariff_code')->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $table->dropUnique(['tariff_code']);
            $table->dropColumn('tariff_code');
        });
    }
};
