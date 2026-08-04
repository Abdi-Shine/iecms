<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attorney_cases', function (Blueprint $table) {
            $table->string('declaration_signature')->nullable()->after('declaration_date');
        });
    }

    public function down(): void
    {
        Schema::table('attorney_cases', function (Blueprint $table) {
            $table->dropColumn('declaration_signature');
        });
    }
};
