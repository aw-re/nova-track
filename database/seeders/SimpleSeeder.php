<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\Project;
use App\Models\Task;
use App\Models\Report;
use App\Models\ProjectMember;

class SimpleSeeder extends Seeder
{
    /**
     * بذر بيانات بسيطة للاختبار
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting simple data seeding...');

        // =============================================
        // 👤 USERS
        // =============================================
        $this->command->info('Creating users...');

        // Get or create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrator', 'description' => 'System Admin']);
        $ownerRole = Role::firstOrCreate(['name' => 'project_owner'], ['display_name' => 'Project Owner', 'description' => 'Project Owner']);
        $engineerRole = Role::firstOrCreate(['name' => 'engineer'], ['display_name' => 'Engineer', 'description' => 'Engineer']);
        $contractorRole = Role::firstOrCreate(['name' => 'contractor'], ['display_name' => 'Contractor', 'description' => 'Contractor']);

        // Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@novatrack.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        // Project Owner
        $owner1 = User::firstOrCreate(
            ['email' => 'owner1@novatrack.com'],
            [
                'name' => 'أحمد المالكي',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'project_owner',
            ]
        );
        $owner1->roles()->syncWithoutDetaching([$ownerRole->id]);

        // Engineer
        $engineer1 = User::firstOrCreate(
            ['email' => 'engineer1@novatrack.com'],
            [
                'name' => 'محمد السعيد',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'engineer',
            ]
        );
        $engineer1->roles()->syncWithoutDetaching([$engineerRole->id]);

        // Contractor
        $contractor1 = User::firstOrCreate(
            ['email' => 'contractor1@novatrack.com'],
            [
                'name' => 'عبدالله القحطاني',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'contractor',
            ]
        );
        $contractor1->roles()->syncWithoutDetaching([$contractorRole->id]);

        $this->command->info('✅ Created 4 users');

        // =============================================
        // 🏗️ PROJECTS
        // =============================================
        $this->command->info('Creating projects...');

        $project1 = Project::firstOrCreate(
            ['name' => 'برج الرياض السكني'],
            [
                'description' => 'مشروع برج سكني فاخر مكون من 30 طابق في قلب مدينة الرياض.',
                'location' => 'الرياض - حي العليا',
                'start_date' => now()->subMonths(3),
                'end_date' => now()->addMonths(18),
                'budget' => 150000000.00,
                'status' => 'in_progress',
                'owner_id' => $owner1->id,
            ]
        );

        $project2 = Project::firstOrCreate(
            ['name' => 'مجمع جدة التجاري'],
            [
                'description' => 'مجمع تجاري متكامل يضم مول تسوق ومكاتب إدارية.',
                'location' => 'جدة - كورنيش البحر الأحمر',
                'start_date' => now()->subMonths(6),
                'end_date' => now()->addMonths(24),
                'budget' => 280000000.00,
                'status' => 'planning',
                'owner_id' => $owner1->id,
            ]
        );

        $this->command->info('✅ Created 2 projects');

        // =============================================
        // 👥 PROJECT MEMBERS
        // =============================================
        $this->command->info('Assigning project members...');

        ProjectMember::firstOrCreate(
            ['project_id' => $project1->id, 'user_id' => $engineer1->id],
            ['role_id' => $engineerRole->id]
        );
        ProjectMember::firstOrCreate(
            ['project_id' => $project1->id, 'user_id' => $contractor1->id],
            ['role_id' => $contractorRole->id]
        );

        $this->command->info('✅ Assigned 2 project members');

        // =============================================
        // 📋 TASKS
        // =============================================
        $this->command->info('Creating tasks...');

        Task::firstOrCreate(
            ['project_id' => $project1->id, 'title' => 'تنفيذ أعمال الأساسات'],
            [
                'description' => 'تنفيذ أعمال الحفر وصب الأساسات الخرسانية',
                'status' => 'completed',
                'priority' => 'high',
                'assigned_to' => $contractor1->id,
                'assigned_by' => $engineer1->id,
                'due_date' => now()->subMonths(2),
            ]
        );

        Task::firstOrCreate(
            ['project_id' => $project1->id, 'title' => 'بناء الهيكل الخرساني'],
            [
                'description' => 'صب الأعمدة والبلاطات الخرسانية',
                'status' => 'in_progress',
                'priority' => 'high',
                'assigned_to' => $contractor1->id,
                'assigned_by' => $engineer1->id,
                'due_date' => now()->addMonths(6),
            ]
        );

        Task::firstOrCreate(
            ['project_id' => $project1->id, 'title' => 'تمديدات الكهرباء'],
            [
                'description' => 'تنفيذ التمديدات الكهربائية',
                'status' => 'todo',
                'priority' => 'medium',
                'assigned_to' => $contractor1->id,
                'assigned_by' => $engineer1->id,
                'due_date' => now()->addMonths(8),
            ]
        );

        $this->command->info('✅ Created 3 tasks');

        // =============================================
        // 📄 REPORTS
        // =============================================
        $this->command->info('Creating reports...');

        Report::firstOrCreate(
            ['project_id' => $project1->id, 'title' => 'تقرير التقدم الأسبوعي'],
            [
                'content' => 'تم إنجاز 45% من الهيكل الخرساني. لا توجد مشاكل كبيرة.',
                'type' => 'weekly',
                'status' => 'approved',
                'created_by' => $engineer1->id,
                'submitted_at' => now()->subDays(7),
                'approved_by' => $owner1->id,
                'approved_at' => now()->subDays(5),
            ]
        );

        Report::firstOrCreate(
            ['project_id' => $project1->id, 'title' => 'تقرير شهري - يناير'],
            [
                'content' => 'ملخص شهري: تم إنجاز الأساسات بالكامل.',
                'type' => 'monthly',
                'status' => 'submitted',
                'created_by' => $engineer1->id,
                'submitted_at' => now()->subMonths(1),
            ]
        );

        $this->command->info('✅ Created 2 reports');

        // =============================================
        // 📊 SUMMARY
        // =============================================
        $this->command->newLine();
        $this->command->info('========================================');
        $this->command->info('🎉 Simple data seeding completed!');
        $this->command->info('========================================');
        $this->command->table(
            ['Entity', 'Count'],
            [
                ['Users', User::count()],
                ['Projects', Project::count()],
                ['Tasks', Task::count()],
                ['Reports', Report::count()],
            ]
        );
        $this->command->newLine();
        $this->command->info('📧 Login Credentials (password: "password"):');
        $this->command->table(
            ['Role', 'Email'],
            [
                ['Admin', 'admin@novatrack.com'],
                ['Owner', 'owner1@novatrack.com'],
                ['Engineer', 'engineer1@novatrack.com'],
                ['Contractor', 'contractor1@novatrack.com'],
            ]
        );
    }
}
