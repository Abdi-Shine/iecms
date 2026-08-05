<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Warrants received FROM courts/AGO directed to CID for execution —
     * the opposite direction from criminal_legal_process_requests
     * (which is CID requesting a warrant be issued). Standalone rather
     * than tied to an existing criminal_case: the suspect may not have
     * a CID case yet when the warrant arrives. criminal_case_id is
     * filled in once execution opens/matches a case.
     */
    public function up(): void
    {
        Schema::create('criminal_received_warrants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();

            $table->string('warrant_number', 100);
            $table->string('issuing_authority', 150);
            $table->string('suspect_name', 150);
            $table->text('suspect_details')->nullable();
            $table->string('offence', 255);
            $table->date('received_date');
            $table->date('warrant_expiry_date')->nullable();

            $table->foreignId('assigned_officer_id')->nullable()
                ->constrained('users')->nullOnDelete()->name('cid_received_warrant_officer_fk');
            $table->string('execution_status', 30)->default('Received');
            // Received | Assigned | Executed | Unserved | Expired | Returned Unexecuted

            $table->foreignId('criminal_case_id')->nullable()
                ->constrained('criminal_cases')->nullOnDelete()->name('cid_received_warrant_case_fk');

            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_received_warrants');
    }
};
