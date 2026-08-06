<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appeal_family_hearings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('family_case_id');
            $table->date('hearing_date');
            $table->time('hearing_time');
            $table->string('duration', 10)->nullable();
            $table->string('courtroom', 50)->nullable();
            $table->text('hearing_purpose')->nullable();
            $table->string('status', 30)->default('Scheduled');
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('family_case_id')->references('AFCID')->on('appeal_family_registrations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appeal_family_hearings');
    }
};
