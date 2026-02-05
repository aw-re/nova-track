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
                'description' => 'System Administrator with full access',
            ],
            [
                'name' => 'project_owner',
                'description' => 'Owner of projects',
            ],
            [
                'name' => 'engineer',
                'description' => 'Project Engineer',
            ],
            [
                'name' => 'contractor',
                'description' => 'Project Contractor',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }

        $this->command->info('Roles seeded successfully!');
    }
}
