<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the Admin User
        $user = \App\Models\User::updateOrCreate(
            ['email' => 'admin@director.gov.so'],
            [
                'name'     => 'Super Administrator',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'position' => 'System Administrator',
                'sex'      => 'Male',
                'phone'    => '1234567890',
                'address'  => 'Mogadishu, Somalia'
            ]
        );

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
