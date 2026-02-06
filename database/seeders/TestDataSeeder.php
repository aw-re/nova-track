<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Project;
use App\Models\Task;
use App\Models\Report;
use App\Models\ResourceRequest;
use App\Models\Resource;
use App\Models\ProjectMember;
use App\Models\TaskUpdate;
use App\Enums\ProjectStatusEnum;
use App\Enums\TaskStatusEnum;
use App\Enums\TaskPriorityEnum;
use App\Enums\ReportStatusEnum;
use App\Enums\ReportTypeEnum;
use App\Enums\ResourceRequestStatusEnum;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting comprehensive test data seeding...');

        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $ownerRole = Role::where('name', 'project_owner')->first();
        $engineerRole = Role::where('name', 'engineer')->first();
        $contractorRole = Role::where('name', 'contractor')->first();

        // =============================================
        // 👤 USERS
        // =============================================
        $this->command->info('Creating users...');

        // Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@novatrack.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );
        $admin->roles()->sync([$adminRole->id]);

        // Project Owners
        $owner1 = User::updateOrCreate(
            ['email' => 'owner1@novatrack.com'],
            [
                'name' => 'أحمد المالكي',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'project_owner',
            ]
        );
        $owner1->roles()->sync([$ownerRole->id]);

        $owner2 = User::updateOrCreate(
            ['email' => 'owner2@novatrack.com'],
            [
                'name' => 'سارة الخالدي',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'project_owner',
            ]
        );
        $owner2->roles()->sync([$ownerRole->id]);

        // Engineers
        $engineer1 = User::updateOrCreate(
            ['email' => 'engineer1@novatrack.com'],
            [
                'name' => 'محمد السعيد',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'engineer',
            ]
        );
        $engineer1->roles()->sync([$engineerRole->id]);

        $engineer2 = User::updateOrCreate(
            ['email' => 'engineer2@novatrack.com'],
            [
                'name' => 'فاطمة العلي',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'engineer',
            ]
        );
        $engineer2->roles()->sync([$engineerRole->id]);

        $engineer3 = User::updateOrCreate(
            ['email' => 'engineer3@novatrack.com'],
            [
                'name' => 'خالد الحربي',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'engineer',
            ]
        );
        $engineer3->roles()->sync([$engineerRole->id]);

        // Contractors
        $contractor1 = User::updateOrCreate(
            ['email' => 'contractor1@novatrack.com'],
            [
                'name' => 'عبدالله القحطاني',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'contractor',
            ]
        );
        $contractor1->roles()->sync([$contractorRole->id]);

        $contractor2 = User::updateOrCreate(
            ['email' => 'contractor2@novatrack.com'],
            [
                'name' => 'يوسف الشمري',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'contractor',
            ]
        );
        $contractor2->roles()->sync([$contractorRole->id]);

        $contractor3 = User::updateOrCreate(
            ['email' => 'contractor3@novatrack.com'],
            [
                'name' => 'نورة الدوسري',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'contractor',
            ]
        );
        $contractor3->roles()->sync([$contractorRole->id]);

        $this->command->info('✅ Created 9 users');

        // =============================================
        // 🏗️ PROJECTS
        // =============================================
        $this->command->info('Creating projects...');

        $project1 = Project::updateOrCreate(
            ['name' => 'برج الرياض السكني'],
            [
                'description' => 'مشروع برج سكني فاخر مكون من 30 طابق في قلب مدينة الرياض. يتضمن المشروع 200 وحدة سكنية مع مرافق ترفيهية ومواقف سيارات.',
                'location' => 'الرياض - حي العليا',
                'start_date' => now()->subMonths(3),
                'end_date' => now()->addMonths(18),
                'budget' => 150000000.00,
                'status' => ProjectStatusEnum::IN_PROGRESS,
                'owner_id' => $owner1->id,
            ]
        );

        $project2 = Project::updateOrCreate(
            ['name' => 'مجمع جدة التجاري'],
            [
                'description' => 'مجمع تجاري متكامل يضم مول تسوق ومكاتب إدارية وفنادق. المشروع يشمل 5 مباني متصلة بجسور.',
                'location' => 'جدة - كورنيش البحر الأحمر',
                'start_date' => now()->subMonths(6),
                'end_date' => now()->addMonths(24),
                'budget' => 280000000.00,
                'status' => ProjectStatusEnum::IN_PROGRESS,
                'owner_id' => $owner1->id,
            ]
        );

        $project3 = Project::updateOrCreate(
            ['name' => 'مستشفى الدمام التخصصي'],
            [
                'description' => 'مستشفى تخصصي سعة 500 سرير مجهز بأحدث التقنيات الطبية. يشمل أقسام طوارئ وعمليات ورعاية مركزة.',
                'location' => 'الدمام - المنطقة الشرقية',
                'start_date' => now()->subMonths(1),
                'end_date' => now()->addMonths(30),
                'budget' => 450000000.00,
                'status' => ProjectStatusEnum::PLANNING,
                'owner_id' => $owner2->id,
            ]
        );

        $project4 = Project::updateOrCreate(
            ['name' => 'منتجع الخبر السياحي'],
            [
                'description' => 'منتجع سياحي فاخر على شاطئ الخليج العربي. يتضمن 150 غرفة فندقية وفلل خاصة ومرافق ترفيهية.',
                'location' => 'الخبر - الواجهة البحرية',
                'start_date' => now()->subMonths(12),
                'end_date' => now()->subMonths(1),
                'budget' => 95000000.00,
                'status' => ProjectStatusEnum::COMPLETED,
                'owner_id' => $owner2->id,
            ]
        );

        $project5 = Project::updateOrCreate(
            ['name' => 'جامعة المستقبل'],
            [
                'description' => 'حرم جامعي متكامل يضم 10 كليات ومرافق بحثية ومكتبة مركزية وسكن طلابي.',
                'location' => 'الرياض - طريق الملك سلمان',
                'start_date' => now(),
                'end_date' => now()->addMonths(36),
                'budget' => 680000000.00,
                'status' => ProjectStatusEnum::PLANNING,
                'owner_id' => $owner1->id,
            ]
        );

        $this->command->info('✅ Created 5 projects');

        // =============================================
        // 👥 PROJECT MEMBERS
        // =============================================
        $this->command->info('Assigning project members...');

        // Project 1 members
        ProjectMember::updateOrCreate(
            ['project_id' => $project1->id, 'user_id' => $engineer1->id],
            ['role_id' => $engineerRole->id]
        );
        ProjectMember::updateOrCreate(
            ['project_id' => $project1->id, 'user_id' => $contractor1->id],
            ['role_id' => $contractorRole->id]
        );
        ProjectMember::updateOrCreate(
            ['project_id' => $project1->id, 'user_id' => $contractor2->id],
            ['role_id' => $contractorRole->id]
        );

        // Project 2 members
        ProjectMember::updateOrCreate(
            ['project_id' => $project2->id, 'user_id' => $engineer2->id],
            ['role_id' => $engineerRole->id]
        );
        ProjectMember::updateOrCreate(
            ['project_id' => $project2->id, 'user_id' => $engineer3->id],
            ['role_id' => $engineerRole->id]
        );
        ProjectMember::updateOrCreate(
            ['project_id' => $project2->id, 'user_id' => $contractor1->id],
            ['role_id' => $contractorRole->id]
        );

        // Project 3 members
        ProjectMember::updateOrCreate(
            ['project_id' => $project3->id, 'user_id' => $engineer1->id],
            ['role_id' => $engineerRole->id]
        );
        ProjectMember::updateOrCreate(
            ['project_id' => $project3->id, 'user_id' => $contractor3->id],
            ['role_id' => $contractorRole->id]
        );

        // Project 4 members
        ProjectMember::updateOrCreate(
            ['project_id' => $project4->id, 'user_id' => $engineer2->id],
            ['role_id' => $engineerRole->id]
        );
        ProjectMember::updateOrCreate(
            ['project_id' => $project4->id, 'user_id' => $contractor2->id],
            ['role_id' => $contractorRole->id]
        );

        // Project 5 members
        ProjectMember::updateOrCreate(
            ['project_id' => $project5->id, 'user_id' => $engineer3->id],
            ['role_id' => $engineerRole->id]
        );
        ProjectMember::updateOrCreate(
            ['project_id' => $project5->id, 'user_id' => $contractor1->id],
            ['role_id' => $contractorRole->id]
        );

        $this->command->info('✅ Assigned 12 project members');

        // =============================================
        // 📋 TASKS
        // =============================================
        $this->command->info('Creating tasks...');

        // Project 1 Tasks
        $task1_1 = Task::updateOrCreate(
            ['project_id' => $project1->id, 'title' => 'تنفيذ أعمال الأساسات'],
            [
                'description' => 'تنفيذ أعمال الحفر وصب الأساسات الخرسانية للبرج السكني',
                'status' => TaskStatusEnum::COMPLETED,
                'priority' => TaskPriorityEnum::HIGH,
                'assigned_to' => $contractor1->id,
                'assigned_by' => $engineer1->id,
                'due_date' => now()->subMonths(2),
            ]
        );

        $task1_2 = Task::updateOrCreate(
            ['project_id' => $project1->id, 'title' => 'بناء الهيكل الخرساني'],
            [
                'description' => 'صب الأعمدة والبلاطات الخرسانية للطوابق الـ 30',
                'status' => TaskStatusEnum::IN_PROGRESS,
                'priority' => TaskPriorityEnum::HIGH,
                'assigned_to' => $contractor1->id,
                'assigned_by' => $engineer1->id,
                'due_date' => now()->addMonths(6),
            ]
        );

        $task1_3 = Task::updateOrCreate(
            ['project_id' => $project1->id, 'title' => 'تمديدات الكهرباء'],
            [
                'description' => 'تنفيذ جميع التمديدات الكهربائية للوحدات السكنية',
                'status' => TaskStatusEnum::TODO,
                'priority' => TaskPriorityEnum::MEDIUM,
                'assigned_to' => $contractor2->id,
                'assigned_by' => $engineer1->id,
                'due_date' => now()->addMonths(8),
            ]
        );

        $task1_4 = Task::updateOrCreate(
            ['project_id' => $project1->id, 'title' => 'أعمال السباكة'],
            [
                'description' => 'تمديد شبكات المياه والصرف الصحي لكامل المبنى',
                'status' => TaskStatusEnum::BACKLOG,
                'priority' => TaskPriorityEnum::MEDIUM,
                'assigned_to' => $contractor2->id,
                'assigned_by' => $engineer1->id,
                'due_date' => now()->addMonths(10),
            ]
        );

        // Project 2 Tasks
        $task2_1 = Task::updateOrCreate(
            ['project_id' => $project2->id, 'title' => 'تصميم واجهات المباني'],
            [
                'description' => 'إعداد التصاميم المعمارية والهندسية لواجهات المجمع التجاري',
                'status' => TaskStatusEnum::COMPLETED,
                'priority' => TaskPriorityEnum::HIGH,
                'assigned_to' => $engineer2->id,
                'assigned_by' => $admin->id,
                'due_date' => now()->subMonths(4),
            ]
        );

        $task2_2 = Task::updateOrCreate(
            ['project_id' => $project2->id, 'title' => 'بناء المبنى الرئيسي'],
            [
                'description' => 'تنفيذ أعمال البناء للمول التجاري الرئيسي',
                'status' => TaskStatusEnum::IN_PROGRESS,
                'priority' => TaskPriorityEnum::URGENT,
                'assigned_to' => $contractor1->id,
                'assigned_by' => $engineer2->id,
                'due_date' => now()->addMonths(12),
            ]
        );

        $task2_3 = Task::updateOrCreate(
            ['project_id' => $project2->id, 'title' => 'أنظمة التكييف المركزي'],
            [
                'description' => 'تركيب نظام التكييف المركزي لكامل المجمع',
                'status' => TaskStatusEnum::TODO,
                'priority' => TaskPriorityEnum::HIGH,
                'assigned_to' => $contractor1->id,
                'assigned_by' => $engineer3->id,
                'due_date' => now()->addMonths(14),
            ]
        );

        // Project 3 Tasks
        $task3_1 = Task::updateOrCreate(
            ['project_id' => $project3->id, 'title' => 'دراسة الجدوى الفنية'],
            [
                'description' => 'إعداد دراسة الجدوى الفنية والاقتصادية للمستشفى',
                'status' => TaskStatusEnum::IN_PROGRESS,
                'priority' => TaskPriorityEnum::HIGH,
                'assigned_to' => $engineer1->id,
                'assigned_by' => $admin->id,
                'due_date' => now()->addWeeks(3),
            ]
        );

        $task3_2 = Task::updateOrCreate(
            ['project_id' => $project3->id, 'title' => 'اختيار المقاولين'],
            [
                'description' => 'فتح المناقصات واختيار المقاولين للمشروع',
                'status' => TaskStatusEnum::BACKLOG,
                'priority' => TaskPriorityEnum::MEDIUM,
                'assigned_to' => null,
                'assigned_by' => $admin->id,
                'due_date' => now()->addMonths(2),
            ]
        );

        // Project 4 Tasks (Completed Project)
        $task4_1 = Task::updateOrCreate(
            ['project_id' => $project4->id, 'title' => 'التشطيبات النهائية'],
            [
                'description' => 'إنهاء جميع أعمال التشطيبات للفلل والغرف الفندقية',
                'status' => TaskStatusEnum::COMPLETED,
                'priority' => TaskPriorityEnum::HIGH,
                'assigned_to' => $contractor2->id,
                'assigned_by' => $engineer2->id,
                'due_date' => now()->subMonths(2),
            ]
        );

        $task4_2 = Task::updateOrCreate(
            ['project_id' => $project4->id, 'title' => 'تجهيز المرافق الترفيهية'],
            [
                'description' => 'تركيب وتشغيل جميع المرافق الترفيهية (مسبح، ملاعب، سبا)',
                'status' => TaskStatusEnum::COMPLETED,
                'priority' => TaskPriorityEnum::MEDIUM,
                'assigned_to' => $contractor2->id,
                'assigned_by' => $engineer2->id,
                'due_date' => now()->subMonths(1),
            ]
        );

        // Project 5 Tasks
        $task5_1 = Task::updateOrCreate(
            ['project_id' => $project5->id, 'title' => 'التصميم المعماري'],
            [
                'description' => 'إعداد التصاميم المعمارية الأولية للحرم الجامعي',
                'status' => TaskStatusEnum::IN_PROGRESS,
                'priority' => TaskPriorityEnum::URGENT,
                'assigned_to' => $engineer3->id,
                'assigned_by' => $admin->id,
                'due_date' => now()->addMonths(2),
            ]
        );

        $this->command->info('✅ Created 13 tasks');

        // =============================================
        // 📄 REPORTS
        // =============================================
        $this->command->info('Creating reports...');

        // Project 1 Reports
        Report::updateOrCreate(
            ['project_id' => $project1->id, 'title' => 'تقرير التقدم الأسبوعي - الأسبوع 12'],
            [
                'content' => 'تم إنجاز 45% من الهيكل الخرساني. لا توجد مشاكل كبيرة. سيتم الانتهاء من الطابق 15 هذا الأسبوع.',
                'type' => ReportTypeEnum::WEEKLY,
                'status' => ReportStatusEnum::APPROVED,
                'created_by' => $engineer1->id,
                'submitted_at' => now()->subDays(7),
                'approved_by' => $owner1->id,
                'approved_at' => now()->subDays(5),
            ]
        );

        Report::updateOrCreate(
            ['project_id' => $project1->id, 'title' => 'تقرير التقدم الأسبوعي - الأسبوع 13'],
            [
                'content' => 'تقدم ممتاز في أعمال البناء. تم صب الطابق 16 و 17. المواد متوفرة بشكل جيد.',
                'type' => ReportTypeEnum::WEEKLY,
                'status' => ReportStatusEnum::SUBMITTED,
                'created_by' => $engineer1->id,
                'submitted_at' => now()->subDays(1),
            ]
        );

        Report::updateOrCreate(
            ['project_id' => $project1->id, 'title' => 'تقرير شهري - يناير 2026'],
            [
                'content' => 'ملخص شهري: تم إنجاز الأساسات بالكامل وبدء أعمال الهيكل الخرساني. نسبة الإنجاز الكلية 25%.',
                'type' => ReportTypeEnum::MONTHLY,
                'status' => ReportStatusEnum::APPROVED,
                'created_by' => $engineer1->id,
                'submitted_at' => now()->subMonths(1),
                'approved_by' => $owner1->id,
                'approved_at' => now()->subMonths(1)->addDays(2),
            ]
        );

        // Project 2 Reports
        Report::updateOrCreate(
            ['project_id' => $project2->id, 'title' => 'تقرير التقدم اليومي'],
            [
                'content' => 'تم استلام شحنة الحديد المسلح. سيتم البدء في صب الأعمدة غداً.',
                'type' => ReportTypeEnum::DAILY,
                'status' => ReportStatusEnum::DRAFT,
                'created_by' => $engineer2->id,
            ]
        );

        Report::updateOrCreate(
            ['project_id' => $project2->id, 'title' => 'تقرير الأسبوع 24'],
            [
                'content' => 'تأخير بسيط في وصول مواد التكسية. تم التنسيق مع الموردين لتسريع الشحن.',
                'type' => ReportTypeEnum::WEEKLY,
                'status' => ReportStatusEnum::SUBMITTED,
                'created_by' => $engineer3->id,
                'submitted_at' => now()->subHours(12),
            ]
        );

        // Project 3 Reports
        Report::updateOrCreate(
            ['project_id' => $project3->id, 'title' => 'تقرير دراسة الجدوى المبدئي'],
            [
                'content' => 'تم الانتهاء من 60% من دراسة الجدوى. النتائج الأولية إيجابية وتشير إلى جدوى المشروع.',
                'type' => ReportTypeEnum::PROGRESS,
                'status' => ReportStatusEnum::SUBMITTED,
                'created_by' => $engineer1->id,
                'submitted_at' => now()->subDays(3),
            ]
        );

        // Project 4 Reports (Completed)
        Report::updateOrCreate(
            ['project_id' => $project4->id, 'title' => 'التقرير النهائي للمشروع'],
            [
                'content' => 'تم الانتهاء من جميع الأعمال بنجاح. المنتجع جاهز للتشغيل. تم تسليم جميع المستندات.',
                'type' => ReportTypeEnum::FINAL ,
                'status' => ReportStatusEnum::APPROVED,
                'created_by' => $engineer2->id,
                'submitted_at' => now()->subMonths(1),
                'approved_by' => $owner2->id,
                'approved_at' => now()->subWeeks(3),
            ]
        );

        $this->command->info('✅ Created 7 reports');

        // =============================================
        // 📦 RESOURCES & REQUESTS
        // =============================================
        $this->command->info('Creating resources and requests...');

        // Create some resources
        $resource1 = Resource::updateOrCreate(
            ['name' => 'إسمنت بورتلاندي'],
            [
                'description' => 'إسمنت عالي الجودة للأعمال الخرسانية',
                'unit' => 'طن',
                'unit_cost' => 350.00,
            ]
        );

        $resource2 = Resource::updateOrCreate(
            ['name' => 'حديد تسليح'],
            [
                'description' => 'حديد تسليح قطر 16 مم',
                'unit' => 'طن',
                'unit_cost' => 2800.00,
            ]
        );

        $resource3 = Resource::updateOrCreate(
            ['name' => 'رمل ناعم'],
            [
                'description' => 'رمل ناعم للخلط الخرساني',
                'unit' => 'متر مكعب',
                'unit_cost' => 85.00,
            ]
        );

        $resource4 = Resource::updateOrCreate(
            ['name' => 'كابلات كهربائية'],
            [
                'description' => 'كابلات كهربائية معزولة 4 مم',
                'unit' => 'متر',
                'unit_cost' => 12.50,
            ]
        );

        // Resource Requests
        ResourceRequest::updateOrCreate(
            ['project_id' => $project1->id, 'resource_name' => 'إسمنت بورتلاندي - دفعة 5'],
            [
                'resource_id' => $resource1->id,
                'task_id' => $task1_2->id,
                'requested_by' => $contractor1->id,
                'resource_type' => 'مواد بناء',
                'quantity' => 500,
                'unit' => 'طن',
                'required_by' => now()->addWeeks(2),
                'description' => 'مطلوب لاستكمال صب الطوابق 18-22',
                'status' => ResourceRequestStatusEnum::APPROVED,
                'approved_by' => $owner1->id,
                'approved_at' => now()->subDays(3),
            ]
        );

        ResourceRequest::updateOrCreate(
            ['project_id' => $project1->id, 'resource_name' => 'حديد تسليح - دفعة 3'],
            [
                'resource_id' => $resource2->id,
                'task_id' => $task1_2->id,
                'requested_by' => $contractor1->id,
                'resource_type' => 'مواد بناء',
                'quantity' => 200,
                'unit' => 'طن',
                'required_by' => now()->addDays(10),
                'description' => 'حديد للأعمدة والبلاطات',
                'status' => ResourceRequestStatusEnum::PENDING,
            ]
        );

        ResourceRequest::updateOrCreate(
            ['project_id' => $project1->id, 'resource_name' => 'كابلات كهربائية'],
            [
                'resource_id' => $resource4->id,
                'task_id' => $task1_3->id,
                'requested_by' => $contractor2->id,
                'resource_type' => 'مواد كهربائية',
                'quantity' => 15000,
                'unit' => 'متر',
                'required_by' => now()->addMonths(2),
                'description' => 'كابلات للتمديدات الكهربائية',
                'status' => ResourceRequestStatusEnum::PENDING,
            ]
        );

        ResourceRequest::updateOrCreate(
            ['project_id' => $project2->id, 'resource_name' => 'خرسانة جاهزة'],
            [
                'resource_id' => null,
                'task_id' => $task2_2->id,
                'requested_by' => $contractor1->id,
                'resource_type' => 'مواد بناء',
                'quantity' => 1500,
                'unit' => 'متر مكعب',
                'required_by' => now()->addWeeks(1),
                'description' => 'خرسانة جاهزة لصب السقف الرئيسي',
                'status' => ResourceRequestStatusEnum::APPROVED,
                'approved_by' => $owner1->id,
                'approved_at' => now()->subDays(1),
            ]
        );

        ResourceRequest::updateOrCreate(
            ['project_id' => $project2->id, 'resource_name' => 'وحدات تكييف مركزي'],
            [
                'resource_id' => null,
                'task_id' => $task2_3->id,
                'requested_by' => $contractor1->id,
                'resource_type' => 'معدات',
                'quantity' => 50,
                'unit' => 'وحدة',
                'required_by' => now()->addMonths(3),
                'description' => 'وحدات تكييف سعة 5 طن للمجمع',
                'status' => ResourceRequestStatusEnum::REJECTED,
                'notes' => 'يجب إعادة تقديم الطلب مع عروض أسعار محدثة',
            ]
        );

        $this->command->info('✅ Created 4 resources and 5 resource requests');

        // =============================================
        // 📝 TASK UPDATES
        // =============================================
        $this->command->info('Creating task updates...');

        TaskUpdate::updateOrCreate(
            ['task_id' => $task1_2->id, 'user_id' => $contractor1->id, 'created_at' => now()->subDays(7)],
            [
                'old_status' => 'todo',
                'new_status' => 'in_progress',
                'comment' => 'بدأنا العمل على الهيكل الخرساني',
                'progress_percentage' => 10,
            ]
        );

        TaskUpdate::updateOrCreate(
            ['task_id' => $task1_2->id, 'user_id' => $contractor1->id, 'created_at' => now()->subDays(3)],
            [
                'description' => 'تم الانتهاء من صب الطوابق 12-15',
                'progress_percentage' => 35,
                'hours_spent' => 120,
            ]
        );

        TaskUpdate::updateOrCreate(
            ['task_id' => $task1_2->id, 'user_id' => $contractor1->id, 'created_at' => now()->subDays(1)],
            [
                'description' => 'تم صب الطابق 16 و 17 بنجاح',
                'progress_percentage' => 45,
                'hours_spent' => 48,
            ]
        );

        TaskUpdate::updateOrCreate(
            ['task_id' => $task2_2->id, 'user_id' => $contractor1->id, 'created_at' => now()->subDays(5)],
            [
                'old_status' => 'backlog',
                'new_status' => 'in_progress',
                'comment' => 'بدء أعمال البناء الرئيسية',
                'progress_percentage' => 15,
            ]
        );

        TaskUpdate::updateOrCreate(
            ['task_id' => $task5_1->id, 'user_id' => $engineer3->id, 'created_at' => now()->subDays(2)],
            [
                'description' => 'تم الانتهاء من التصميم الأولي للمكتبة المركزية',
                'progress_percentage' => 25,
                'hours_spent' => 40,
            ]
        );

        $this->command->info('✅ Created 5 task updates');

        // =============================================
        // 📊 SUMMARY
        // =============================================
        $this->command->newLine();
        $this->command->info('========================================');
        $this->command->info('🎉 Test data seeding completed!');
        $this->command->info('========================================');
        $this->command->table(
            ['Entity', 'Count'],
            [
                ['Users', User::count()],
                ['Projects', Project::count()],
                ['Tasks', Task::count()],
                ['Reports', Report::count()],
                ['Resource Requests', ResourceRequest::count()],
                ['Resources', Resource::count()],
                ['Task Updates', TaskUpdate::count()],
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
