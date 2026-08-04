<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('district_civil_appeals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('civil_case_id');
            $table->string('appeal_type')->nullable();
            $table->date('appeal_date')->nullable();
            $table->json('appealing_parties')->nullable();
            $table->text('additional_notes')->nullable();
            $table->string('attachment')->nullable();
            $table->string('status')->default('Draft');
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('civil_case_id')->references('CRID')->on('distric_civil_registrations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('district_civil_appeals');
    }
};
