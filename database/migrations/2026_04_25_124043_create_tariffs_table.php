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
        Schema::create('tariffs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_so')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('amount_text')->nullable(); // For "To be determined" or "Amount of fine"
            $table->string('type')->default('Fixed'); // Fixed, Variable
            $table->string('level')->nullable();
            $table->string('currency')->default('USD');
            $table->string('status')->default('Active');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tariffs');
    }
};
