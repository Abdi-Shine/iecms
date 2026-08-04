<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('document_signatures');

        if (Schema::hasColumn('district_civil_close', 'judgment_id')) {
            Schema::table('district_civil_close', function ($table) {
                $table->dropColumn(['judgment_id', 'verdict', 'reasoning', 'additional_notes', 'judgment_attachment']);
            });
        }
    }

    public function down(): void {}
};
