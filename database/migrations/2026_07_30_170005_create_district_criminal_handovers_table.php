<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('district_criminal_handovers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('criminal_case_id');
            $table->json('documents')->nullable();
            $table->text('special_instructions')->nullable();
            $table->text('additional_notes')->nullable();
            $table->string('status')->default('Draft');
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('criminal_case_id')->references('CMID')->on('district_criminal_registrations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('district_criminal_handovers');
    }
};
