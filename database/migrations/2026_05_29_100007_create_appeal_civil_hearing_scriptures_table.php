<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appeal_civil_hearing_scriptures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('civil_case_id');
            $table->unsignedBigInteger('hearing_id')->nullable();
            $table->string('form_id')->nullable();
            $table->integer('session_number')->default(1);
            $table->date('hearing_date')->nullable();
            $table->time('hearing_time')->nullable();
            $table->string('courtroom')->nullable();
            $table->string('hearing_type')->nullable();
            $table->longText('body_content')->nullable();
            $table->string('status', 30)->default('Draft');
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('civil_case_id')->references('ACID')->on('appeal_civil_registrations')->onDelete('cascade');
            $table->foreign('hearing_id')->references('id')->on('appeal_civil_hearings')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appeal_civil_hearing_scriptures');
    }
};
