<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Employees only picked up an institution indirectly, through a linked
     * User account (employees.system_username = users.email). That breaks
     * institution scoping for employees who don't have login access yet —
     * exactly the staff the "Grant Access" flow needs to find. Adding the
     * column directly lets an employee be scoped to an institution from
     * creation, independent of login status.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('institution_id')->nullable()->after('courtID');
            $table->foreign('institution_id')->references('id')->on('institutions')->nullOnDelete();
        });

        // Backfill from each employee's linked User account, where one exists.
        DB::statement('
            UPDATE employees
            INNER JOIN users ON users.email = employees.system_username
            SET employees.institution_id = users.institution_id
            WHERE users.institution_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['institution_id']);
            $table->dropColumn('institution_id');
        });
    }
};
