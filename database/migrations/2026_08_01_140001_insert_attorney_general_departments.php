<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $departments = [
        'Office of the Attorney General',
        'Office of the Deputy Attorney General',
        'Criminal Prosecution Department',
        'Civil Litigation Department',
        'Appeals Department',
        'General Crimes Unit',
        'Homicide and Serious Crimes Unit',
        'Sexual and Gender-Based Violence (SGBV) Unit',
        'Juvenile Crimes Unit',
        'Narcotics and Dangerous Drugs Unit',
        'Human Trafficking and Immigration Crimes Unit',
        'Finance and Economic Crimes Unit',
        'Electronic and Cyber Crimes Unit',
        'Environmental Crimes Unit',
        'Maritime Crimes Unit',
        'Crimes Against the State and Public Order Unit',
        'Special Prosecution Unit',
        'Civil Prosecution Unit',
        'Police Investigation Coordination Section',
        'Witness Protection and Evidence Management Section',
        'Exhibit Management Section',
        'Complaints Registration and Case Intake Section',
        'Mutual Legal Assistance Section',
        'Legal Research and Law Reform Department',
        'Execution of Judgments Section',
        'Prisons and Detention Oversight Department',
        'Administration and Finance Department',
        'Human Resources Department',
        'ICT Department',
        'Planning and Development Department',
        'Communications and Public Relations Department',
        'Library and Legal Research Department',
        'Archives and Records Management Department',
        'Asset Management Section',
        'State and Regional Affairs Department',
    ];

    public function up(): void
    {
        $now = now();

        DB::table('attorney_departments')->insertOrIgnore(
            array_map(fn ($name) => [
                'name'       => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ], $this->departments)
        );
    }

    public function down(): void
    {
        DB::table('attorney_departments')->whereIn('name', $this->departments)->delete();
    }
};
