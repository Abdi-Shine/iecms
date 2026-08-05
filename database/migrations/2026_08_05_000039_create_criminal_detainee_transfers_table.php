<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criminal_detainee_transfers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('detainee_id')->constrained('criminal_detainees')
                ->cascadeOnDelete()->name('cid_transfer_detainee_fk');

            $table->string('from_facility', 150);
            $table->string('to_facility', 150);
            $table->dateTime('transfer_datetime');
            $table->text('reason')->nullable();
            $table->string('escorting_officer', 150);
            $table->boolean('receiving_officer_confirmed')->default(false);

            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_detainee_transfers');
    }
};
