<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('district_civil_judgment_receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('judgment_id');
            $table->unsignedBigInteger('party_id');
            $table->string('party_name');
            $table->string('party_role', 50)->nullable();
            $table->string('judgment_outcome')->nullable();
            $table->date('received_date')->nullable();
            $table->string('signature')->nullable();
            $table->string('rep_name')->nullable();
            $table->string('rep_signature')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->string('received_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('judgment_id')->references('id')->on('district_civil_judgments')->onDelete('cascade');
            $table->unique(['judgment_id', 'party_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('district_civil_judgment_receipts');
    }
};
