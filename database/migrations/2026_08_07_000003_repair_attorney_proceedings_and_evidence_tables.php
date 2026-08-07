<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Repairs two AGO tables left broken by an earlier database corruption
     * incident (see backups/mysql_broken_backup_2026-08-04):
     *
     * - attorney_proceedings: MySQL/MariaDB metadata says it exists, but its
     *   InnoDB data file is gone ("doesn't exist in engine", error 1932).
     *   Its creation migration (2026_07_31_150006) never actually completed
     *   on this database, so it's harmless to drop and recreate.
     * - attorney_evidence: never created at all (its creation migration,
     *   2026_07_31_150007, is genuinely pending); simply missing.
     *
     * Both are guarded so this is a no-op on a database where those
     * migrations already ran cleanly (fresh installs, or environments
     * without the 2026-08-04 corruption) — it only acts on a table that's
     * either absent or confirmed broken.
     *
     * The court_id FK on attorney_proceedings now points at `judiciaries`
     * (the courts table was renamed in 2026_08_07_000002_rename_courts_
     * table_to_judiciaries), unlike the original 2026_07_31_150006
     * migration which still correctly targets `courts` for a fresh install
     * that hasn't reached the rename yet.
     */
    public function up(): void
    {
        if ($this->isBroken('attorney_proceedings')) {
            Schema::dropIfExists('attorney_proceedings');
        }

        if (!Schema::hasTable('attorney_proceedings')) {
            Schema::create('attorney_proceedings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('attorney_case_id');
                $table->unsignedInteger('court_id')->nullable();
                $table->unsignedBigInteger('judge_id')->nullable();
                $table->date('hearing_date');
                $table->string('hearing_time', 20)->nullable();
                $table->string('proceeding_type', 50)->default('Arraignment');
                $table->text('outcome')->nullable();
                $table->string('status', 30)->default('Scheduled');
                $table->text('notes')->nullable();
                $table->string('created_by', 100)->nullable();
                $table->timestamps();

                $table->foreign('attorney_case_id', 'ag_proceedings_case_id_fk')
                    ->references('ACID')->on('attorney_cases')->onDelete('cascade');
                $table->foreign('court_id')->references('CAI')->on('judiciaries')->nullOnDelete();
                $table->foreign('judge_id', 'ag_proceedings_judge_id_fk')
                    ->references('AID')->on('employees')->nullOnDelete();
            });
        }

        if ($this->isBroken('attorney_evidence')) {
            Schema::dropIfExists('attorney_evidence');
        }

        if (!Schema::hasTable('attorney_evidence')) {
            Schema::create('attorney_evidence', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('attorney_case_id');
                $table->string('evidence_type', 30)->default('Physical');
                $table->text('description');
                $table->string('collected_by', 100)->nullable();
                $table->date('collected_date')->nullable();
                $table->string('custody_holder', 100)->nullable();
                $table->string('storage_location', 150)->nullable();
                $table->string('file_path')->nullable();
                $table->string('status', 30)->default('In Custody');
                $table->timestamps();

                $table->foreign('attorney_case_id', 'ag_evidence_case_id_fk')
                    ->references('ACID')->on('attorney_cases')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_evidence');
        Schema::dropIfExists('attorney_proceedings');
    }

    private function isBroken(string $table): bool
    {
        if (!Schema::hasTable($table)) {
            return false;
        }

        try {
            DB::select("select 1 from `{$table}` limit 1");

            return false;
        } catch (\Throwable $e) {
            return true;
        }
    }
};
