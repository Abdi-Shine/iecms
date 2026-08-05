<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criminal_detainee_releases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('detainee_id')->unique()->constrained('criminal_detainees')
                ->cascadeOnDelete()->name('cid_release_detainee_fk');

            $table->string('release_type', 40);
            // Court Order | Bail Granted | Charges Dropped | Expiry of Remand Period | Transfer to Correctional Services
            $table->string('authorizing_officer', 150);
            $table->string('release_document_reference', 150)->nullable();
            $table->text('release_conditions')->nullable();
            $table->string('receiving_party', 150)->nullable();
            $table->boolean('property_returned_confirmed')->default(false);
            $table->dateTime('released_at');

            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_detainee_releases');
    }
};
