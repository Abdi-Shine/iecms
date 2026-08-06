<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appeal_criminal_lawyers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('criminal_case_id');
            $table->unsignedBigInteger('lawyer_id');
            $table->unsignedBigInteger('party_id')->nullable();
            $table->string('party_role')->nullable();
            $table->string('lawyer_type')->nullable();
            $table->date('assignment_date')->nullable();
            $table->text('reason')->nullable();
            $table->string('status')->default('Active');
            $table->string('addedBy')->nullable();
            $table->timestamp('addedDate')->nullable();
            $table->timestamps();

            $table->foreign('criminal_case_id')->references('ACMID')->on('appeal_criminal_registrations')->onDelete('cascade');
            $table->foreign('lawyer_id')->references('LRID')->on('lawyers')->onDelete('cascade');
            $table->foreign('party_id')->references('PID')->on('appeal_criminal_parties')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appeal_criminal_lawyers');
    }
};
