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
        Schema::create('district_civil_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('civil_case_id');
            $table->string('payer_name');
            $table->string('payer_phone')->nullable();
            $table->string('payer_email')->nullable();
            $table->unsignedInteger('court_id')->nullable();
            $table->unsignedBigInteger('tariff_id')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('currency')->default('USD');
            $table->date('payment_date')->nullable();
            $table->string('status')->default('Pending'); // Pending, Awaiting Approval, Approved, Failed
            $table->unsignedBigInteger('cashier_id')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('civil_case_id')->references('CRID')->on('distric_civil_registrations')->onDelete('cascade');
            $table->foreign('court_id')->references('CAI')->on('courts')->nullOnDelete();
            $table->foreign('tariff_id')->references('id')->on('tariffs')->nullOnDelete();
            $table->foreign('cashier_id')->references('AID')->on('employees')->nullOnDelete();
            $table->foreign('approved_by')->references('AID')->on('employees')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('district_civil_payments');
    }
};
