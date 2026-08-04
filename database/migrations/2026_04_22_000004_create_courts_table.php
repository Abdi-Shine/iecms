<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('courts', function (Blueprint $table) {
            $table->increments('CAI');
            $table->string('courtcode', 20)->unique();
            $table->string('shortName', 20);
            $table->string('longName', 100);
            $table->string('arabic_name', 150)->nullable();
            $table->string('Grade_levels', 25);
            $table->string('status', 50)->default('active');
            $table->text('remarks')->nullable();
            $table->string('logo', 100)->nullable();
            $table->string('stamp', 150)->nullable();
            $table->string('letterhead')->nullable();
            $table->text('address')->nullable();
            $table->string('telephone', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('website')->nullable();
            $table->string('addedBy', 50)->nullable();
            $table->timestamp('addedDate')->useCurrent();
            $table->string('updatedBy', 25)->nullable();
            $table->string('updatedDate', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courts');
    }
};
