<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('backup_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed defaults
        DB::table('backup_settings')->insert([
            ['key' => 'auto_backup_enabled', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'backup_retention',    'value' => '30', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'backup_time',         'value' => '02:00', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_settings');
    }
};
