<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payer_name');
            $table->decimal('amount', 15, 2);
            $table->string('currency')->default('USD');
            $table->string('status')->default('Pending');
            $table->date('payment_date')->nullable();
            $table->string('transaction_id')->unique()->nullable();
            $table->unsignedBigInteger('tariff_id')->nullable();
            $table->unsignedInteger('court_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tariff_id')->references('id')->on('tariffs')->onDelete('set null');
            $table->foreign('court_id')->references('CAI')->on('courts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
