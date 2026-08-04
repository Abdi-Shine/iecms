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
        Schema::create('employees', function (Blueprint $table) {
            $table->id('AID');
            $table->string('SUID', 100);
            $table->string('EmpID', 100);
            $table->string('EmpName', 100);
            $table->string('gender', 25);
            $table->string('phone', 25);
            $table->string('email', 100);
            $table->string('photo', 150);
            $table->string('signature', 255)->nullable();
            $table->date('DOB');
            $table->string('POB', 100);
            $table->string('Position', 100);
            $table->string('courtID', 100);
            $table->string('status', 10);
            $table->string('islogin', 100);
            $table->string('system_username', 100)->nullable();
            $table->string('system_role', 50)->nullable();
            $table->date('Dates');
            $table->string('addedBy', 50);
            $table->string('updatedBy', 50);
            $table->string('updatedDate', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
