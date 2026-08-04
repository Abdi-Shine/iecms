<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_case_parties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attorney_case_id');
            $table->string('party_role', 50);
            $table->string('full_name', 150);
            $table->string('contact_number', 30)->nullable();
            $table->string('national_id', 50)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();

            $table->foreign('attorney_case_id')->references('ACID')->on('attorney_cases')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_parties');
    }
};
