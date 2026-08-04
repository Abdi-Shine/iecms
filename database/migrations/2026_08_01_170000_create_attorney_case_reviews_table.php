<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_case_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attorney_case_id');
            $table->foreignId('attorney_department_id')->nullable()->constrained('attorney_departments')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('comment');
            $table->date('review_date');
            $table->timestamps();

            $table->foreign('attorney_case_id')->references('ACID')->on('attorney_cases')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_reviews');
    }
};
