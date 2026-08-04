<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('timezone', 60)->default('Africa/Mogadishu')->after('address');
            $table->string('date_format', 20)->default('d/m/Y')->after('timezone');
            $table->unsignedSmallInteger('items_per_page')->default(20)->after('date_format');
            $table->string('language', 10)->default('en')->after('items_per_page');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['timezone', 'date_format', 'items_per_page', 'language']);
        });
    }
};
