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
        Schema::create('district_execution_enforcements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('execution_case_id')->unique();
            $table->string('enforcement_type')->nullable();
            $table->date('enforcement_date')->nullable();
            $table->text('description')->nullable();
            $table->text('orders')->nullable();
            $table->text('additional_notes')->nullable();
            $table->string('attachment')->nullable();
            $table->string('status')->default('Draft');
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('execution_case_id')
                ->references('ECID')
                ->on('district_execution_registrations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('district_execution_enforcements');
    }
};
