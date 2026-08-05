<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criminal_detainee_property_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('detainee_id')->constrained('criminal_detainees')
                ->cascadeOnDelete()->name('cid_property_item_detainee_fk');

            $table->string('item_description', 255);
            $table->integer('quantity')->default(1);
            $table->boolean('returned')->default(false);
            $table->dateTime('returned_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_detainee_property_items');
    }
};
