<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_case_legal_provisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attorney_case_id');
            $table->string('provision', 255);
            $table->timestamps();

            $table->foreign('attorney_case_id', 'ag_legal_provisions_case_id_fk')
                ->references('ACID')->on('attorney_cases')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_case_legal_provisions');
    }
};
