<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Supports the "Foomka Codsiga Lacag Bixinta" (Applicant Payment Request)
     * form embedded on the civil case page: links a payment to the case it
     * was requested from, and records the applicant's contact info plus the
     * treasurer (Qasnajiga) who will process it.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('civil_case_id')->nullable()->after('id');
            $table->string('payer_phone')->nullable()->after('payer_name');
            $table->string('payer_email')->nullable()->after('payer_phone');
            $table->unsignedBigInteger('cashier_id')->nullable()->after('court_id');

            $table->foreign('civil_case_id')->references('CRID')->on('distric_civil_registrations')->nullOnDelete();
            $table->foreign('cashier_id')->references('AID')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['civil_case_id']);
            $table->dropForeign(['cashier_id']);
            $table->dropColumn(['civil_case_id', 'payer_phone', 'payer_email', 'cashier_id']);
        });
    }
};
