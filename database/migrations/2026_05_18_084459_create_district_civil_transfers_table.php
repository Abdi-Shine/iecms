<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('district_civil_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('civil_case_id');
            $table->string('from_court')->nullable();
            $table->string('to_court');
            $table->date('transfer_date')->nullable();
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable();
            $table->string('status')->default('Draft');
            $table->string('created_by')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('civil_case_id')
                  ->references('CRID')
                  ->on('distric_civil_registrations')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('district_civil_transfers');
    }
};
