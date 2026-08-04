<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('title_documents_attachments', function (Blueprint $table) {
            $table->id('AID');
            $table->string('Acode', 50)->unique();
            $table->string('Aname', 100);
            $table->string('courtID', 50)->nullable();
            $table->string('addedBy', 50)->nullable();
            $table->string('addedDate', 50)->nullable();
            $table->string('updatedBy', 50)->nullable();
            $table->string('updatedDate', 25)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_attachments');
    }
};
