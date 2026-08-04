<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attorney_investigation_updates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attorney_investigation_id');
            $table->text('note');
            $table->string('added_by', 100)->nullable();
            $table->timestamps();

            $table->foreign('attorney_investigation_id', 'ag_inv_updates_inv_id_fk')
                ->references('id')->on('attorney_investigations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attorney_investigation_updates');
    }
};
