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
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'System Administrator with full access',
            ],
            [
                'name' => 'project_owner',
                'display_name' => 'Project Owner',
                'description' => 'Owner of projects who can manage and approve',
            ],
            [
                'name' => 'engineer',
                'display_name' => 'Engineer',
                'description' => 'Project Engineer who assigns and manages tasks',
            ],
            [
                'name' => 'contractor',
                'display_name' => 'Contractor',
                'description' => 'Contractor who executes tasks and requests resources',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }

        $this->command->info('✅ Roles seeded successfully!');
    }
}

