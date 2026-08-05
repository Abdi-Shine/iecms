<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Renewal history via self-referencing renewal_of — each renewal is
     * a new row pointing at the order it extends, rather than mutating
     * the original (matches the append-only pattern used elsewhere,
     * e.g. court appearance outcomes via next_hearing_date).
     */
    public function up(): void
    {
        Schema::create('criminal_detainee_remand_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('detainee_id')->constrained('criminal_detainees')
                ->cascadeOnDelete()->name('cid_remand_detainee_fk');
            $table->foreignId('renewal_of')->nullable()
                ->constrained('criminal_detainee_remand_orders')->nullOnDelete()->name('cid_remand_renewal_fk');

            $table->string('court_reference', 150);
            $table->string('judge', 150)->nullable();
            $table->string('remand_period', 50);
            $table->date('remand_start_date');
            $table->date('expiry_date');
            $table->string('court_order_path', 255)->nullable();

            $table->string('added_by', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criminal_detainee_remand_orders');
    }
};
