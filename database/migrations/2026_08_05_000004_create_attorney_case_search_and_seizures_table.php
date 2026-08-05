<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_case_search_and_seizures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attorney_case_id')->constrained('attorney_cases', 'ACID')->cascadeOnDelete();

            $table->string('location_to_search')->nullable();
            $table->text('items_sought')->nullable();
            $table->text('grounds_for_search')->nullable();
            $table->date('application_date');
            $table->string('applying_officer')->nullable();
            $table->string('warrant_status')->default('Sugaya');
            $table->string('warrant_number')->nullable();
            $table->date('search_conducted_date')->nullable();
            $table->text('items_seized')->nullable();
            $table->string('search_conducted_by')->nullable();
            $table->string('witnesses_present')->nullable();
            $table->boolean('property_receipt_issued')->default(false);
            $table->string('search_report_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_search_and_seizures');
    }
};
