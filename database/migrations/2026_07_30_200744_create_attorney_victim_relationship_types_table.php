<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_victim_relationship_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('value', 100)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        $now = now();
        \Illuminate\Support\Facades\DB::table('attorney_victim_relationship_types')->insert([
            ['name' => 'Hooyo', 'value' => 'hooyo', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Aabo', 'value' => 'aabo', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Walaal', 'value' => 'walaal', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ayeeyo/Awoowe', 'value' => 'ayeeyo-awoowe', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Qaraabo Kale', 'value' => 'qaraabo-kale', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Masuul Kale', 'value' => 'masuul-kale', 'description' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_victim_relationship_types');
    }
};
