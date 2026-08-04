<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drops the English name, amount note, and level fields from the Tariff
     * form/table — Service (Somali) is now the sole name field used
     * everywhere a tariff's name is displayed.
     */
    public function up(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $table->dropColumn(['name', 'amount_text', 'level']);
        });
    }

    public function down(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $table->string('name')->nullable()->after('court_id');
            $table->string('amount_text')->nullable()->after('amount');
            $table->string('level')->nullable()->after('type');
        });
    }
};
