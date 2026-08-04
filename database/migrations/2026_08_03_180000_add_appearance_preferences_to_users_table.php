<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('theme', 10)->default('light')->after('language');
            $table->string('font_size', 10)->default('md')->after('theme');
            $table->boolean('collapse_sidebar')->default(false)->after('font_size');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['theme', 'font_size', 'collapse_sidebar']);
        });
    }
};
