<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'admin@director.gov.so';

        if (\App\Models\User::where('email', $email)->exists()) {
            $this->command?->info("Admin user $email already exists, skipping.");
            return;
        }

        $password = Str::password(20);

        // Create the Admin User
        \App\Models\User::updateOrCreate(
            ['email' => $email],
            [
                'name'     => 'Super Administrator',
                'password' => \Illuminate\Support\Facades\Hash::make($password),
                'position' => 'System Administrator',
                'sex'      => 'Male',
                'phone'    => '1234567890',
                'address'  => 'Mogadishu, Somalia'
            ]
        );

        $this->command?->info("Admin user created: $email / $password");

        // Create the corresponding Employee record for permissions/sidebar visibility
        \App\Models\Employee::updateOrCreate(
            ['email' => 'admin@director.gov.so'],
            [
                'AID'             => 1, // Optional: handle PK manually if needed, but let DB handle if preferred
                'EmpID'           => 'EMP-001',
                'EmpName'         => 'Super Administrator',
                'gender'          => 'Male',
                'phone'           => '1234567890',
                'photo'           => 'default.png',
                'DOB'             => '1990-01-01',
                'POB'             => 'Mogadishu',
                'Position'        => 'System Administrator',
                'courtID'         => '1', // Default to first court
                'status'          => 'Active',
                'islogin'         => '1',
                'system_username' => 'admin@director.gov.so',
                'system_role'     => 'admin',
                'Dates'           => now(),
                'addedBy'         => 'System',
                'updatedBy'       => 'System',
                'updatedDate'     => now(),
            ]
        );
    }
}
