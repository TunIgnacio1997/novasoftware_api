<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Administrador'],
            ['name' => 'Administrador'],
            ['name' => 'Vendedor'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }
    }
}
