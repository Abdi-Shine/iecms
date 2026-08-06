<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appeal_family_close', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('family_case_id');
            $table->string('judgment_type')->nullable();
            $table->date('judgment_date')->nullable();
            $table->text('decision_body')->nullable();
            $table->string('status')->default('Draft');
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('family_case_id')->references('AFCID')->on('appeal_family_registrations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appeal_family_close');
    }
};
