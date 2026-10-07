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
            ['nombre' => 'Super Administrador'],
            ['nombre' => 'Administrador'],
            ['nombre' => 'Vendedor'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }
    }
}
