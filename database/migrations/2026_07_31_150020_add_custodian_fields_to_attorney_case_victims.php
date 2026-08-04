<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attorney_case_victims', function (Blueprint $table) {
            $table->string('custodian_full_name', 150)->nullable()->after('impact_description');
            $table->string('custodian_relationship', 100)->nullable()->after('custodian_full_name');
            $table->string('custodian_id_number', 50)->nullable()->after('custodian_relationship');
            $table->string('custodian_phone_number', 30)->nullable()->after('custodian_id_number');
            $table->text('custodian_address')->nullable()->after('custodian_phone_number');
            $table->string('custodian_email', 150)->nullable()->after('custodian_address');
        });
    }

    public function down(): void
    {
        Schema::table('attorney_case_victims', function (Blueprint $table) {
            $table->dropColumn([
                'custodian_full_name', 'custodian_relationship', 'custodian_id_number',
                'custodian_phone_number', 'custodian_address', 'custodian_email',
            ]);
        });
    }
};
