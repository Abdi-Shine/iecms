<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Investigation Takeovers — formal case ownership transfer. The
     * spec requires four gates before a transfer executes: reason,
     * outgoing officer acknowledgment, incoming officer acceptance, and
     * admin approval. The actual reassignment (updating the case's
     * assignment/OB investigator) only happens at the final admin
     * approval step, in CriminalCaseTakeoverController::approve.
     */
    public function up(): void
    {
        Schema::create('criminal_case_takeovers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('criminal_case_id')->constrained('criminal_cases')->cascadeOnDelete();

            $table->text('reason');
            $table->foreignId('outgoing_investigator_id')->nullable()
                ->constrained('users')->nullOnDelete()->name('cid_takeover_outgoing_fk');
            $table->foreignId('incoming_investigator_id')
                ->constrained('users')->cascadeOnDelete()->name('cid_takeover_incoming_fk');

            $table->timestamp('outgoing_acknowledged_at')->nullable();
            $table->timestamp('incoming_accepted_at')->nullable();

            $table->foreignId('admin_approved_by')->nullable()
                ->constrained('users')->nullOnDelete()->name('cid_takeover_admin_fk');
            $table->timestamp('admin_approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_case_takeovers');
    }
};
