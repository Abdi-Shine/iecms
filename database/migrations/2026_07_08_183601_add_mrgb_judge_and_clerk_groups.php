<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Group;
use App\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $garsoore = Role::where('name', 'Garsoore')->first();
        $kaaliye  = Role::where('name', 'Kaaliye')->first();

        if ($garsoore && !Group::where('name', 'MRGBGarsoorayasha')->exists()) {
            $group = Group::create([
                'name'        => 'MRGBGarsoorayasha',
                'description' => 'Garsoorayaasha Maxkamadda Rafcaanka Gobolka Banaadir',
                'status'      => 'active',
                'addedBy'     => 'System',
                'addedDate'   => now(),
            ]);
            $group->roles()->attach($garsoore->id);
        }

        if ($kaaliye && !Group::where('name', 'MRGBKaaliyaasha')->exists()) {
            $group = Group::create([
                'name'        => 'MRGBKaaliyaasha',
                'description' => 'Kaaliyayaasha Maxkamadda Rafcaanka Gobolka Banaadir',
                'status'      => 'active',
                'addedBy'     => 'System',
                'addedDate'   => now(),
            ]);
            $group->roles()->attach($kaaliye->id);
        }
    }

    public function down(): void
    {
        Group::whereIn('name', ['MRGBGarsoorayasha', 'MRGBKaaliyaasha'])->delete();
    }
};
