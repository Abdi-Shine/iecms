<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_case_investigation_extension_accused', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investigation_extension_id')
                ->constrained('attorney_case_investigation_extensions')
                ->cascadeOnDelete()
                ->name('acie_accused_extension_id_foreign');

            $table->string('full_name');
            $table->string('mother_name')->nullable();
            $table->string('sex')->nullable();
            $table->string('residence')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_investigation_extension_accused');
    }
};
