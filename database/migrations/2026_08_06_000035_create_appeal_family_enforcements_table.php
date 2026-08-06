<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appeal_family_enforcements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('family_case_id')->unique();
            $table->string('enforcement_type')->nullable();
            $table->date('enforcement_date')->nullable();
            $table->text('description')->nullable();
            $table->text('orders')->nullable();
            $table->text('additional_notes')->nullable();
            $table->string('attachment')->nullable();
            $table->string('status')->default('Draft');
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('family_case_id')->references('AFCID')->on('appeal_family_registrations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appeal_family_enforcements');
    }
};
