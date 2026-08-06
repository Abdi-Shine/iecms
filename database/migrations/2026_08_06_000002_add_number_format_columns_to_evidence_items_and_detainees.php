<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('criminal_case_evidence_items', function (Blueprint $table) {
            $table->string('evidence_id', 40)->nullable()->after('id')->index();
        });

        Schema::table('criminal_detainees', function (Blueprint $table) {
            $table->string('detainee_number', 40)->nullable()->after('id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('criminal_case_evidence_items', function (Blueprint $table) {
            $table->dropColumn('evidence_id');
        });

        Schema::table('criminal_detainees', function (Blueprint $table) {
            $table->dropColumn('detainee_number');
        });
    }
};
