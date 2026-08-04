<?php

use App\Models\Group;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * "Garsoore", "Kaaliye" and "Kaaliyaha Sare" were each shared between a
     * District (MDB) group and an Appeal (MRGB) group. Because permissions
     * live on the Role, not the Group, this meant every user in either group
     * got BOTH District and Appeal case-management access at once. This
     * splits each shared role in two — a District-only role (kept under the
     * original name) and a new "<Role> Rafcaanka" role carrying only the
     * Appeal-side case-management permissions plus the shared/admin ones —
     * mirroring how Chairman already has two distinct roles
     * (Gudoomiye Dagmo / Gudoomiyaha Maxkamadda).
     */
    public function up(): void
    {
        // Case-management modules that have a district/appeal counterpart.
        // These are the only ones that get separated; everything else
        // (Dashboard, Staff Registry, Judicial Units, etc.) is shared admin
        // data and stays on both roles.
        $districtCaseModules = [
            'Civil Case Registration', 'Case Handover', 'Case Assignment', 'Hearings',
            'Judgments', 'Close Case', 'Return Civil File', 'Enforcement',
            'Case Parties', 'Case Documents', 'Case Lawyers',
        ];

        $splits = [
            'Garsoore'       => ['name' => 'Garsoore Rafcaanka',       'color' => '#D97706'],
            'Kaaliye'        => ['name' => 'Kaaliye Rafcaanka',        'color' => '#4B5563'],
            'Kaaliyaha Sare' => ['name' => 'Kaaliyaha Sare Rafcaanka', 'color' => '#78716C'],
        ];

        $groupRepoint = [
            'MRGBGarsoorayasha'  => 'Garsoore Rafcaanka',
            'MRGBKaaliyaasha'    => 'Kaaliye Rafcaanka',
            'MRGBKaaliyaha Sare' => 'Kaaliyaha Sare Rafcaanka',
        ];

        foreach ($splits as $originalName => $new) {
            $originalRole = Role::where('name', $originalName)->first();
            if (!$originalRole) {
                continue;
            }

            $newRole = Role::firstOrCreate(
                ['name' => $new['name']],
                ['display_name' => $new['name'], 'color' => $new['color']]
            );

            $originalPermissions = $originalRole->permissions;

            $sharedAdminPermissions = $originalPermissions
                ->reject(fn ($p) => str_starts_with($p->module, 'Appeal'))
                ->reject(fn ($p) => in_array($p->module, $districtCaseModules));

            $appealPermissions = $originalPermissions
                ->filter(fn ($p) => str_starts_with($p->module, 'Appeal'));

            $newRole->permissions()->sync(
                $sharedAdminPermissions->pluck('id')->merge($appealPermissions->pluck('id'))
            );

            // Original role keeps district-case + shared/admin permissions,
            // loses the Appeal-prefixed ones.
            $originalRole->permissions()->sync(
                $originalPermissions->reject(fn ($p) => str_starts_with($p->module, 'Appeal'))->pluck('id')
            );
        }

        foreach ($groupRepoint as $groupName => $newRoleName) {
            $group = Group::where('name', $groupName)->first();
            $newRole = Role::where('name', $newRoleName)->first();
            if ($group && $newRole) {
                $group->roles()->sync([$newRole->id]);
            }
        }
    }

    public function down(): void
    {
        $groupRestore = [
            'MRGBGarsoorayasha'  => 'Garsoore',
            'MRGBKaaliyaasha'    => 'Kaaliye',
            'MRGBKaaliyaha Sare' => 'Kaaliyaha Sare',
        ];

        foreach ($groupRestore as $groupName => $originalRoleName) {
            $group = Group::where('name', $groupName)->first();
            $originalRole = Role::where('name', $originalRoleName)->first();
            if ($group && $originalRole) {
                $group->roles()->sync([$originalRole->id]);
            }
        }

        foreach (['Garsoore Rafcaanka', 'Kaaliye Rafcaanka', 'Kaaliyaha Sare Rafcaanka'] as $name) {
            Role::where('name', $name)->delete();
        }

        // Note: this does not restore the Appeal permissions that were
        // removed from the original District roles.
    }
};
