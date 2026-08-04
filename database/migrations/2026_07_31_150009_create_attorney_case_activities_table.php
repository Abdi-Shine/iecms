<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_case_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attorney_case_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type', 30);
            $table->string('description', 255);
            $table->timestamps();

            $table->foreign('attorney_case_id', 'ag_case_activities_case_id_fk')
                ->references('ACID')->on('attorney_cases')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_activities');
    }
};
