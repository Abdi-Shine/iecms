<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attorney_case_accused', function (Blueprint $table) {
            $table->string('mother_name', 150)->nullable()->after('full_name');
            $table->string('gender', 20)->nullable()->after('date_of_birth');
            $table->string('place_of_birth', 150)->nullable()->after('gender');
            $table->string('district', 100)->nullable()->after('address');
            $table->string('region', 100)->nullable()->after('district');
            $table->text('additional_details')->nullable()->after('id_number');
        });
    }

    public function down(): void
    {
        Schema::table('attorney_case_accused', function (Blueprint $table) {
            $table->dropColumn(['mother_name', 'gender', 'place_of_birth', 'district', 'region', 'additional_details']);
        });
    }
};
