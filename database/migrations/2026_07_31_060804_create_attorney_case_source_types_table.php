<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Custom "Source of Case" options added via the wizard's "+" modal,
     * on top of the 4 built-in types (Lawyer/Individual/Government
     * Agency/Company) which stay hardcoded since they drive dedicated
     * field sets in the wizard's Alpine logic.
     */
    public function up(): void
    {
        Schema::create('attorney_case_source_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('value', 100)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_source_types');
    }
};
