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
        Schema::create('district_family_registrations', function (Blueprint $table) {
            $table->bigIncrements('FCID');
            $table->string('RegisterNo', 50)->default('');
            $table->string('FileNo', 50)->default('');
            $table->string('GradeCourt', 50)->default('');
            $table->string('CaseType', 50)->default('');
            $table->string('SubCase', 100)->nullable();
            $table->date('OpenDate');
            $table->string('NumberLetter', 50)->nullable();
            $table->text('LegalBasis')->default('');
            $table->text('Orders_Requested')->default('');
            $table->text('Remarks')->default('');
            $table->string('Status', 50)->default('');
            $table->string('addedBy', 50)->default('');
            $table->string('addedDate', 50)->default('');
            $table->string('updatedBy', 50)->nullable();
            $table->string('updatedDate', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('district_family_registrations');
    }
};
