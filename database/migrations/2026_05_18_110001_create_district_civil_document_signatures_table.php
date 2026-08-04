<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('district_civil_document_signatures')) {
            return;
        }

        Schema::create('district_civil_document_signatures', function (Blueprint $table) {
            $table->id();
            $table->string('document_type', 50);
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('signer_id');
            $table->string('role', 30)->default('signer');
            $table->timestamp('signed_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('content_hash', 64)->nullable();
            $table->timestamps();

            $table->unique(['document_type', 'document_id', 'signer_id'], 'doc_sig_unique');
            $table->foreign('signer_id')->references('AID')->on('employees')->onDelete('cascade');
            $table->index(['document_type', 'document_id'], 'doc_type_id_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('district_civil_document_signatures');
    }
};
