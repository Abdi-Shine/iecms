<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criminal_detainee_medical_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('detainee_id')->constrained('criminal_detainees')
                ->cascadeOnDelete()->name('cid_medical_detainee_fk');

            $table->date('visit_date');
            $table->string('visited_by', 150);
            $table->text('screening_notes')->nullable();
            $table->text('ongoing_conditions')->nullable();
            $table->text('medications')->nullable();
            $table->string('referral_to', 150)->nullable();
            $table->boolean('is_emergency')->default(false);

            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_detainee_medical_records');
    }
};
