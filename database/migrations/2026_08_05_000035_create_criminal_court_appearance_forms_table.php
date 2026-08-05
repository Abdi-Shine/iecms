<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Standardized court forms (witness summons, officer subpoena,
     * production order, attendance form) — auto-populated from the
     * case and optionally tied to a specific hearing.
     */
    public function up(): void
    {
        Schema::create('criminal_court_appearance_forms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('criminal_case_id')->constrained('criminal_cases')->cascadeOnDelete();
            $table->foreignId('court_appearance_id')->nullable()
                ->constrained('criminal_case_court_appearances')->nullOnDelete()->name('cid_court_form_appearance_fk');

            $table->string('form_type', 30); // witness_summons | officer_subpoena | production_order | attendance_form
            $table->string('recipient_name', 150);
            $table->string('recipient_role', 50)->nullable();

            $table->string('status', 20)->default('Generated'); // Generated | Served | Acknowledged | Defaulted
            $table->date('served_date')->nullable();
            $table->string('served_by', 150)->nullable();

            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_court_appearance_forms');
    }
};
