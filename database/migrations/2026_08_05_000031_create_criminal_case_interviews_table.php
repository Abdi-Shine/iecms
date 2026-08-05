<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menu 4's Interviews sub-menu — suspect/witness/complainant/
     * informant sessions. interviewee_id is widened to text and
     * encrypted at the model layer (same convention as AttorneyCaseParty
     * ::national_id). Restricted to Investigator/Admin at the
     * controller level, same pattern as Biometrics.
     */
    public function up(): void
    {
        Schema::create('criminal_case_interviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('criminal_case_id')->constrained('criminal_cases')->cascadeOnDelete();

            $table->string('interviewee_name', 150);
            $table->text('interviewee_id')->nullable();
            $table->string('interviewee_role', 20); // suspect | witness | complainant | informant

            $table->date('interview_date');
            $table->time('interview_time')->nullable();
            $table->string('interview_location', 255)->nullable();
            $table->string('interviewing_officer', 150);
            $table->string('second_officer_witness', 150)->nullable();

            $table->string('format', 20); // written | audio | video | transcribed
            $table->string('recording_file_path', 255)->nullable();
            $table->text('statement_summary')->nullable();

            $table->foreignId('signed_off_by')->nullable()
                ->constrained('users')->nullOnDelete()->name('cid_interview_signoff_fk');
            $table->timestamp('signed_off_at')->nullable();

            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_case_interviews');
    }
};
