<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $statusMap = [
        'Reported'            => 'Diiwaan',
        'Under Investigation' => 'Baaritaan Socda',
        'Charges Filed'       => 'La Eedeeyay',
        'In Court'            => 'Ku Jira Maxkamadda',
        'Convicted'           => 'La Xukumay',
        'Acquitted'           => 'La Sii Daayay',
        'Dismissed'           => 'La Diiday',
        'Closed'              => 'La Xiray',
    ];

    private array $priorityMap = [
        'Low'    => 'Hoose',
        'Medium' => 'Dhexdhexaad',
        'High'   => 'Sarreeya',
        'Urgent' => 'Degdeg ah',
    ];

    private array $sourceOfCaseMap = [
        'Individual'         => 'Shakhsi',
        'Lawyer'             => 'Qareen',
        'Company'            => 'Shirkad',
        'Government Agency'  => "Hay'ad Dawladeed",
    ];

    public function up(): void
    {
        foreach ($this->statusMap as $english => $somali) {
            DB::table('attorney_cases')->where('status', $english)->update(['status' => $somali]);
        }
        foreach ($this->priorityMap as $english => $somali) {
            DB::table('attorney_cases')->where('priority', $english)->update(['priority' => $somali]);
        }
        foreach ($this->sourceOfCaseMap as $english => $somali) {
            DB::table('attorney_cases')->where('source_of_case', $english)->update(['source_of_case' => $somali]);
        }
    }

    public function down(): void
    {
        foreach ($this->statusMap as $english => $somali) {
            DB::table('attorney_cases')->where('status', $somali)->update(['status' => $english]);
        }
        foreach ($this->priorityMap as $english => $somali) {
            DB::table('attorney_cases')->where('priority', $somali)->update(['priority' => $english]);
        }
        foreach ($this->sourceOfCaseMap as $english => $somali) {
            DB::table('attorney_cases')->where('source_of_case', $somali)->update(['source_of_case' => $english]);
        }
    }
};
