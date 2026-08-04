<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('district_criminal_judgments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('criminal_case_id');
            $table->string('form_id')->nullable();
            $table->date('judgment_date')->nullable();
            $table->time('judgment_time')->nullable();
            $table->string('judgment_type')->nullable();
            $table->text('judgment_body')->nullable();
            $table->string('status', 30)->default('Draft');
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('criminal_case_id')->references('CMID')->on('district_criminal_registrations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('district_criminal_judgments');
    }
};
