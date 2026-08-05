<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Real chain-of-custody history per evidence item — every transfer
     * appended, never edited. This is the actual audit trail the CID
     * spec's "Chain of custody log per evidence item" asks for.
     */
    public function up(): void
    {
        Schema::create('criminal_case_evidence_custody_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('evidence_item_id')->constrained('criminal_case_evidence_items')
                ->cascadeOnDelete()->name('cid_custody_log_evidence_fk');
            $table->string('from_officer', 150)->nullable();
            $table->string('to_officer', 150);
            $table->dateTime('transferred_at');
            $table->string('reason', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_case_evidence_custody_logs');
    }
};
