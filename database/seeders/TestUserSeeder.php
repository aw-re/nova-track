<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Project;
use App\Models\Task;
use App\Models\Report;
use App\Models\ResourceRequest;
use App\Models\File;
use App\Models\ActivityLog;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $ownerRole = Role::where('name', 'project_owner')->first();
        $engineerRole = Role::where('name', 'engineer')->first();
        $contractorRole = Role::where('name', 'contractor')->first();

        // Create test users for each role using updateOrCreate to avoid duplicates
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        $adminUser->roles()->sync([$adminRole->id]);

        $ownerUser = User::updateOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'Project Owner',
                'password' => Hash::make('password'),
            ]
        );
        $ownerUser->roles()->sync([$ownerRole->id]);

        $engineerUser = User::updateOrCreate(
            ['email' => 'engineer@example.com'],
            [
                'name' => 'Engineer User',
                'password' => Hash::make('password'),
            ]
        );
        $engineerUser->roles()->sync([$engineerRole->id]);

        $contractorUser = User::updateOrCreate(
            ['email' => 'contractor@example.com'],
            [
                'name' => 'Contractor User',
                'password' => Hash::make('password'),
            ]
        );
        $contractorUser->roles()->sync([$contractorRole->id]);

        // Log the creation of test users
        ActivityLog::create([
            'user_id' => $adminUser->id,
            'action' => 'created',
            'description' => 'Test users created or updated for demonstration',
            'ip_address' => '127.0.0.1',
        ]);

        // Output the test user credentials
        $this->command->info('Test users created or updated:');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Project Owner: owner@example.com / password');
        $this->command->info('Engineer: engineer@example.com / password');
        $this->command->info('Contractor: contractor@example.com / password');
    }
}
