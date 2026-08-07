<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CidAdminUserSeeder extends Seeder
{
    /**
     * Creates the dedicated CID Admin account and attaches it to the
     * "CID Admins" group. The institution, groups, roles, and permissions
     * themselves are seeded by the 2026_08_05_* / 2026_08_06_* migrations —
     * this seeder only needs to exist so the account survives a fresh
     * database rebuild.
     */
    public function run(): void
    {
        $email = 'cid.admin@iecms.gov.so';

        if (\App\Models\User::where('email', $email)->exists()) {
            $this->command?->info("CID Admin user $email already exists, skipping.");
            return;
        }

        $group = \App\Models\Group::where('name', 'CID Admins')
            ->whereHas('institution', fn ($q) => $q->where('type', 'cid'))
            ->first();

        if (!$group) {
            $this->command?->warn('CID Admins group not found — run the CID institution migrations first.');
            return;
        }

        $password = Str::password(20);

        \App\Models\User::create([
            'name'       => 'CID Administrator',
            'email'      => $email,
            'password'   => Hash::make($password),
            'position'   => 'CID Institution Administrator',
            'sex'        => 'Male',
            'address'    => 'Mogadishu, Somalia',
            'group_id'   => $group->id,
            'institution_id' => $group->institution_id,
        ]);

        $this->command?->info("CID Admin user created: $email / $password");
    }
}
