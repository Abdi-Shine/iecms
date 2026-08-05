<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Detention Center's Exhibit Management — physical exhibits held at
     * the facility pending trial. Linked to the case (spec says "case
     * reference"), not the detainee, since a case can have exhibits
     * held even after the detainee is released/transferred. Uses a
     * simpler current-holder + status pattern (not a full history
     * table like evidence custody logs) given the smaller scope of
     * this sub-feature.
     */
    public function up(): void
    {
        Schema::create('criminal_case_exhibits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('criminal_case_id')->constrained('criminal_cases')->cascadeOnDelete();

            $table->string('description', 255);
            $table->string('receiving_officer', 150);
            $table->string('storage_location', 150)->nullable();
            $table->string('condition', 100)->nullable();
            $table->string('status', 20)->default('Held'); // Held | Sent to Court | Returned | Disposed
            $table->string('current_holder', 150)->nullable();

            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_case_exhibits');
    }
};
