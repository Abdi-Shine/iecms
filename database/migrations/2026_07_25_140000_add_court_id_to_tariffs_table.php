<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lets a tariff be scoped to a specific court (e.g. filing fees differ by
     * court). Nullable — a tariff with no court applies to all courts.
     */
    public function up(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $table->unsignedInteger('court_id')->nullable()->after('id');
            $table->foreign('court_id')->references('CAI')->on('courts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $table->dropForeign(['court_id']);
            $table->dropColumn('court_id');
        });
    }
};
