<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('district_execution_lawyers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('execution_case_id');
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

            $table->foreign('execution_case_id')->references('ECID')->on('district_execution_registrations')->onDelete('cascade');
            $table->foreign('lawyer_id')->references('LRID')->on('lawyers')->onDelete('cascade');
            $table->foreign('party_id')->references('PID')->on('district_execution_parties')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('district_execution_lawyers');
    }
};
