<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Task;
use App\Models\Resource;
use App\Models\ResourceRequest;
use App\Models\File;
use App\Models\Report;
use App\Models\Notification;
use App\Models\Rating;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Administrator']);
        $ownerRole = Role::create(['name' => 'project_owner', 'display_name' => 'Project Owner']);
        $engineerRole = Role::create(['name' => 'engineer', 'display_name' => 'Engineer']);
        $contractorRole = Role::create(['name' => 'contractor', 'display_name' => 'Contractor']);

        // Create users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone' => '1234567890',
            'address' => '123 Admin St, City',
            'company' => 'CPMS Admin',
        ]);
        $admin->roles()->attach($adminRole->id);

        $owner = User::create([
            'name' => 'Project Owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
            'phone' => '2345678901',
            'address' => '456 Owner Ave, City',
            'company' => 'Construction Co.',
        ]);
        $owner->roles()->attach($ownerRole->id);

        $engineer1 = User::create([
            'name' => 'Engineer One',
            'email' => 'engineer1@example.com',
            'password' => Hash::make('password'),
            'phone' => '3456789012',
            'address' => '789 Engineer Blvd, City',
            'company' => 'Engineering Solutions',
        ]);
        $engineer1->roles()->attach($engineerRole->id);

        $engineer2 = User::create([
            'name' => 'Engineer Two',
            'email' => 'engineer2@example.com',
            'password' => Hash::make('password'),
            'phone' => '4567890123',
            'address' => '101 Engineer Lane, City',
            'company' => 'Tech Engineers Inc.',
        ]);
        $engineer2->roles()->attach($engineerRole->id);

        $contractor1 = User::create([
            'name' => 'Contractor One',
            'email' => 'contractor1@example.com',
            'password' => Hash::make('password'),
            'phone' => '5678901234',
            'address' => '202 Contractor St, City',
            'company' => 'Build It Right',
        ]);
        $contractor1->roles()->attach($contractorRole->id);

        $contractor2 = User::create([
            'name' => 'Contractor Two',
            'email' => 'contractor2@example.com',
            'password' => Hash::make('password'),
            'phone' => '6789012345',
            'address' => '303 Contractor Ave, City',
            'company' => 'Quality Contractors',
        ]);
        $contractor2->roles()->attach($contractorRole->id);

        // Create resources
        $resources = [
            ['name' => 'Cement', 'description' => 'Portland cement', 'category' => 'Building Materials', 'unit' => 'Bags'],
            ['name' => 'Steel Rebar', 'description' => 'Reinforcement steel bars', 'category' => 'Building Materials', 'unit' => 'Tons'],
            ['name' => 'Bricks', 'description' => 'Standard clay bricks', 'category' => 'Building Materials', 'unit' => 'Pieces'],
            ['name' => 'Sand', 'description' => 'Fine sand for concrete mixing', 'category' => 'Building Materials', 'unit' => 'Cubic Meters'],
            ['name' => 'Excavator', 'description' => 'Heavy machinery for digging', 'category' => 'Equipment', 'unit' => 'Hours'],
            ['name' => 'Crane', 'description' => 'Tower crane for lifting materials', 'category' => 'Equipment', 'unit' => 'Days'],
            ['name' => 'Lumber', 'description' => 'Construction grade lumber', 'category' => 'Building Materials', 'unit' => 'Board Feet'],
            ['name' => 'Paint', 'description' => 'Interior/exterior paint', 'category' => 'Finishing Materials', 'unit' => 'Gallons'],
        ];

        $resourceMap = [];
        foreach ($resources as $index => $resourceData) {
            $resource = Resource::create($resourceData);
            $resourceMap[$index + 1] = $resource->id;
        }

        // Create Project 1
        $project1 = Project::create([
            'name' => 'Commercial Office Building',
            'description' => 'Construction of a 10-story commercial office building in downtown area',
            'location' => 'Downtown Business District',
            'start_date' => now()->subMonths(2),
            'end_date' => now()->addMonths(10),
            'budget' => 5000000.00,
            'status' => 'in_progress',
            'owner_id' => $owner->id,
        ]);

        // Add project members
        ProjectMember::create([
            'project_id' => $project1->id,
            'user_id' => $owner->id,
            'role_id' => $ownerRole->id,
            'status' => 'active',
            'joined_at' => now()->subMonths(2),
        ]);

        ProjectMember::create([
            'project_id' => $project1->id,
            'user_id' => $engineer1->id,
            'role_id' => $engineerRole->id,
            'status' => 'active',
            'joined_at' => now()->subMonths(2),
        ]);

        ProjectMember::create([
            'project_id' => $project1->id,
            'user_id' => $contractor1->id,
            'role_id' => $contractorRole->id,
            'status' => 'active',
            'joined_at' => now()->subMonths(2),
        ]);

        // Create tasks for Project 1
        $tasks1 = [
            [
                'title' => 'Site Preparation',
                'description' => 'Clear the site and prepare for foundation work',
                'project_id' => $project1->id,
                'assigned_by' => $engineer1->id,
                'assigned_to' => $contractor1->id,
                'priority' => 'high',
                'status' => 'completed',
                'start_date' => now()->subMonths(2),
                'due_date' => now()->subMonths(1)->subDays(15),
                'estimated_hours' => 120,
                'actual_hours' => 110,
                'completed_at' => now()->subMonths(1)->subDays(17),
            ],
            [
                'title' => 'Foundation Construction',
                'description' => 'Excavate and construct the building foundation',
                'project_id' => $project1->id,
                'assigned_by' => $engineer1->id,
                'assigned_to' => $contractor1->id,
                'priority' => 'high',
                'status' => 'completed',
                'start_date' => now()->subMonths(1)->subDays(14),
                'due_date' => now()->subDays(15),
                'estimated_hours' => 240,
                'actual_hours' => 260,
                'completed_at' => now()->subDays(12),
            ],
            [
                'title' => 'Structural Framework',
                'description' => 'Construct the main structural framework of the building',
                'project_id' => $project1->id,
                'assigned_by' => $engineer1->id,
                'assigned_to' => $contractor1->id,
                'priority' => 'high',
                'status' => 'in_progress',
                'start_date' => now()->subDays(10),
                'due_date' => now()->addMonths(1),
                'estimated_hours' => 500,
                'actual_hours' => 120,
            ],
            [
                'title' => 'Electrical Wiring Planning',
                'description' => 'Create detailed plans for the electrical wiring system',
                'project_id' => $project1->id,
                'assigned_by' => $engineer1->id,
                'assigned_to' => null,
                'priority' => 'medium',
                'status' => 'todo',
                'start_date' => now()->addDays(5),
                'due_date' => now()->addDays(20),
                'estimated_hours' => 80,
            ],
        ];

        $taskMap = [];
        foreach ($tasks1 as $index => $taskData) {
            $task = Task::create($taskData);
            $taskMap["project1_" . ($index + 1)] = $task->id;
        }

        // Create resource requests for Project 1
        $resourceRequests1 = [
            [
                'project_id' => $project1->id,
                'task_id' => $taskMap["project1_1"], // Site Preparation
                'resource_id' => $resourceMap[5], // Excavator
                'requested_by' => $contractor1->id,
                'quantity' => 40,
                'status' => 'delivered',
                'requested_date' => now()->subMonths(2),
                'required_date' => now()->subMonths(2)->addDays(5),
                'notes' => 'Need excavator for site clearing',
                'approved_by' => $owner->id,
                'approved_at' => now()->subMonths(2)->addDays(1),
                'delivered_at' => now()->subMonths(2)->addDays(3),
            ],
            [
                'project_id' => $project1->id,
                'task_id' => $taskMap["project1_2"], // Foundation Construction
                'resource_id' => $resourceMap[1], // Cement
                'requested_by' => $contractor1->id,
                'quantity' => 500,
                'status' => 'delivered',
                'requested_date' => now()->subMonths(1)->subDays(20),
                'required_date' => now()->subMonths(1)->subDays(10),
                'notes' => 'For foundation construction',
                'approved_by' => $owner->id,
                'approved_at' => now()->subMonths(1)->subDays(19),
                'delivered_at' => now()->subMonths(1)->subDays(12),
            ],
            [
                'project_id' => $project1->id,
                'task_id' => $taskMap["project1_2"], // Foundation Construction
                'resource_id' => $resourceMap[2], // Steel Rebar
                'requested_by' => $contractor1->id,
                'quantity' => 15,
                'status' => 'delivered',
                'requested_date' => now()->subMonths(1)->subDays(20),
                'required_date' => now()->subMonths(1)->subDays(10),
                'notes' => 'For foundation reinforcement',
                'approved_by' => $owner->id,
                'approved_at' => now()->subMonths(1)->subDays(19),
                'delivered_at' => now()->subMonths(1)->subDays(12),
            ],
            [
                'project_id' => $project1->id,
                'task_id' => $taskMap["project1_3"], // Structural Framework
                'resource_id' => $resourceMap[6], // Crane
                'requested_by' => $contractor1->id,
                'quantity' => 30,
                'status' => 'approved',
                'requested_date' => now()->subDays(15),
                'required_date' => now()->subDays(5),
                'notes' => 'Need crane for structural framework',
                'approved_by' => $owner->id,
                'approved_at' => now()->subDays(14),
            ],
            [
                'project_id' => $project1->id,
                'task_id' => $taskMap["project1_3"], // Structural Framework
                'resource_id' => $resourceMap[2], // Steel Rebar
                'requested_by' => $contractor1->id,
                'quantity' => 25,
                'status' => 'pending',
                'requested_date' => now()->subDays(3),
                'required_date' => now()->addDays(5),
                'notes' => 'Additional steel for upper floors',
            ],
        ];

        foreach ($resourceRequests1 as $request) {
            ResourceRequest::create($request);
        }

        // Create Project 2
        $project2 = Project::create([
            'name' => 'Residential Apartment Complex',
            'description' => 'Construction of a 5-building residential apartment complex with 120 units',
            'location' => 'Suburban Area, North District',
            'start_date' => now()->subMonths(1),
            'end_date' => now()->addYears(1),
            'budget' => 12000000.00,
            'status' => 'in_progress',
            'owner_id' => $owner->id,
        ]);

        // Add project members
        ProjectMember::create([
            'project_id' => $project2->id,
            'user_id' => $owner->id,
            'role_id' => $ownerRole->id,
            'status' => 'active',
            'joined_at' => now()->subMonths(1),
        ]);

        ProjectMember::create([
            'project_id' => $project2->id,
            'user_id' => $engineer2->id,
            'role_id' => $engineerRole->id,
            'status' => 'active',
            'joined_at' => now()->subMonths(1),
        ]);

        ProjectMember::create([
            'project_id' => $project2->id,
            'user_id' => $contractor2->id,
            'role_id' => $contractorRole->id,
            'status' => 'active',
            'joined_at' => now()->subMonths(1),
        ]);

        // Create tasks for Project 2
        $tasks2 = [
            [
                'title' => 'Site Survey and Analysis',
                'description' => 'Complete topographical survey and soil analysis',
                'project_id' => $project2->id,
                'assigned_by' => $engineer2->id,
                'assigned_to' => $contractor2->id,
                'priority' => 'high',
                'status' => 'completed',
                'start_date' => now()->subMonths(1),
                'due_date' => now()->subDays(20),
                'estimated_hours' => 60,
                'actual_hours' => 55,
                'completed_at' => now()->subDays(22),
            ],
            [
                'title' => 'Architectural Design Finalization',
                'description' => 'Finalize all architectural designs and get approvals',
                'project_id' => $project2->id,
                'assigned_by' => $engineer2->id,
                'assigned_to' => null,
                'priority' => 'high',
                'status' => 'in_progress',
                'start_date' => now()->subDays(15),
                'due_date' => now()->addDays(15),
                'estimated_hours' => 120,
                'actual_hours' => 80,
            ],
            [
                'title' => 'Permit Acquisition',
                'description' => 'Obtain all necessary construction permits from local authorities',
                'project_id' => $project2->id,
                'assigned_by' => $engineer2->id,
                'assigned_to' => $contractor2->id,
                'priority' => 'high',
                'status' => 'in_progress',
                'start_date' => now()->subDays(10),
                'due_date' => now()->addDays(20),
                'estimated_hours' => 40,
                'actual_hours' => 15,
            ],
            [
                'title' => 'Site Preparation',
                'description' => 'Clear the site and prepare for foundation work',
                'project_id' => $project2->id,
                'assigned_by' => $engineer2->id,
                'assigned_to' => $contractor2->id,
                'priority' => 'medium',
                'status' => 'todo',
                'start_date' => now()->addDays(25),
                'due_date' => now()->addDays(45),
                'estimated_hours' => 200,
            ],
        ];

        foreach ($tasks2 as $index => $taskData) {
            $task = Task::create($taskData);
            $taskMap["project2_" . ($index + 1)] = $task->id;
        }

        // Create resource requests for Project 2
        $resourceRequests2 = [
            [
                'project_id' => $project2->id,
                'task_id' => $taskMap["project2_1"], // Site Survey and Analysis
                'resource_id' => $resourceMap[5], // Excavator
                'requested_by' => $contractor2->id,
                'quantity' => 10,
                'status' => 'delivered',
                'requested_date' => now()->subMonths(1),
                'required_date' => now()->subMonths(1)->addDays(5),
                'notes' => 'Need excavator for site survey',
                'approved_by' => $owner->id,
                'approved_at' => now()->subMonths(1)->addDays(1),
                'delivered_at' => now()->subMonths(1)->addDays(3),
            ],
            [
                'project_id' => $project2->id,
                'task_id' => $taskMap["project2_4"], // Site Preparation
                'resource_id' => $resourceMap[5], // Excavator
                'requested_by' => $contractor2->id,
                'quantity' => 80,
                'status' => 'approved',
                'requested_date' => now()->subDays(5),
                'required_date' => now()->addDays(20),
                'notes' => 'Need excavator for full site preparation',
                'approved_by' => $owner->id,
                'approved_at' => now()->subDays(3),
            ],
        ];

        foreach ($resourceRequests2 as $request) {
            ResourceRequest::create($request);
        }

        // Create files for both projects
        $files = [
            [
                'project_id' => $project1->id,
                'task_id' => null,
                'uploaded_by' => $owner->id,
                'file_name' => 'project1_blueprint.pdf',
                'file_path' => 'project-files/1/project1_blueprint.pdf',
                'file_type' => 'application/pdf',
                'file_size' => 2500000,
                'description' => 'Main blueprint for the commercial office building',
                'version' => '1.0',
            ],
            [
                'project_id' => $project1->id,
                'task_id' => $taskMap["project1_2"], // Foundation Construction
                'uploaded_by' => $engineer1->id,
                'file_name' => 'foundation_design.pdf',
                'file_path' => 'project-files/1/foundation_design.pdf',
                'file_type' => 'application/pdf',
                'file_size' => 1800000,
                'description' => 'Detailed foundation design specifications',
                'version' => '2.1',
            ],
            [
                'project_id' => $project1->id,
                'task_id' => $taskMap["project1_3"], // Structural Framework
                'uploaded_by' => $engineer1->id,
                'file_name' => 'structural_specs.pdf',
                'file_path' => 'project-files/1/structural_specs.pdf',
                'file_type' => 'application/pdf',
                'file_size' => 3200000,
                'description' => 'Structural framework specifications',
                'version' => '1.0',
            ],
            [
                'project_id' => $project2->id,
                'task_id' => null,
                'uploaded_by' => $owner->id,
                'file_name' => 'apartment_complex_masterplan.pdf',
                'file_path' => 'project-files/2/apartment_complex_masterplan.pdf',
                'file_type' => 'application/pdf',
                'file_size' => 4500000,
                'description' => 'Master plan for the residential apartment complex',
                'version' => '1.0',
            ],
            [
                'project_id' => $project2->id,
                'task_id' => $taskMap["project2_2"], // Architectural Design Finalization
                'uploaded_by' => $engineer2->id,
                'file_name' => 'architectural_designs.pdf',
                'file_path' => 'project-files/2/architectural_designs.pdf',
                'file_type' => 'application/pdf',
                'file_size' => 5200000,
                'description' => 'Complete architectural designs for all buildings',
                'version' => '0.9',
            ],
        ];

        foreach ($files as $file) {
            File::create($file);
        }

        // Create reports
        $reports = [
            [
                'project_id' => $project1->id,
                'created_by' => $engineer1->id,
                'title' => 'Monthly Progress Report - Month 1',
                'content' => "# Monthly Progress Report\n\n## Project Overview\nThe Commercial Office Building project has completed its first month of construction. Site preparation and initial foundation work have been completed according to schedule.\n\n## Completed Tasks\n- Site clearing and preparation\n- Initial excavation\n- Foundation marking\n\n## In Progress\n- Foundation construction\n\n## Upcoming Work\n- Structural framework\n- Basement construction\n\n## Issues and Concerns\nNo major issues to report. Weather conditions have been favorable for construction activities.",
                'type' => 'monthly',
                'status' => 'approved',
                'submitted_at' => now()->subDays(15),
                'approved_by' => $owner->id,
                'approved_at' => now()->subDays(13),
            ],
            [
                'project_id' => $project1->id,
                'created_by' => $engineer1->id,
                'title' => 'Monthly Progress Report - Month 2',
                'content' => "# Monthly Progress Report\n\n## Project Overview\nThe Commercial Office Building project has completed its second month of construction. Foundation work has been completed and structural framework has begun.\n\n## Completed Tasks\n- Foundation construction\n- Basement waterproofing\n- Ground floor slab\n\n## In Progress\n- Structural framework\n- Column installation\n\n## Upcoming Work\n- Electrical wiring planning\n- Plumbing rough-in\n\n## Issues and Concerns\nFoundation work took 3 days longer than expected due to unexpected soil conditions, but we've adjusted the schedule to compensate.",
                'type' => 'monthly',
                'status' => 'submitted',
                'submitted_at' => now()->subDays(2),
            ],
            [
                'project_id' => $project2->id,
                'created_by' => $engineer2->id,
                'title' => 'Initial Project Report',
                'content' => "# Initial Project Report\n\n## Project Overview\nThe Residential Apartment Complex project has begun with site survey and analysis. Initial findings show favorable conditions for construction.\n\n## Completed Tasks\n- Site survey and soil analysis\n- Initial meetings with local authorities\n\n## In Progress\n- Architectural design finalization\n- Permit acquisition\n\n## Upcoming Work\n- Site preparation\n- Foundation planning\n\n## Issues and Concerns\nSome concerns about drainage in the northeast corner of the site. Additional analysis recommended.",
                'type' => 'progress',
                'status' => 'approved',
                'submitted_at' => now()->subDays(10),
                'approved_by' => $owner->id,
                'approved_at' => now()->subDays(8),
            ],
        ];

        foreach ($reports as $report) {
            Report::create($report);
        }

        // Create notifications
        $notifications = [
            [
                'user_id' => $owner->id,
                'title' => 'New Report Submitted',
                'message' => 'Engineer One has submitted a new monthly report for the Commercial Office Building project.',
                'type' => 'report',
                'related_id' => 2, // Monthly Progress Report - Month 2
                'related_type' => 'report',
                'is_read' => false,
            ],
            [
                'user_id' => $contractor1->id,
                'title' => 'Resource Request Approved',
                'message' => 'Your request for Crane has been approved by Project Owner.',
                'type' => 'resource',
                'related_id' => 4, // Crane request for Project 1
                'related_type' => 'resource_request',
                'is_read' => true,
                'read_at' => now()->subDays(13),
            ],
            [
                'user_id' => $engineer1->id,
                'title' => 'Task Completed',
                'message' => 'Contractor One has marked the "Foundation Construction" task as completed.',
                'type' => 'task',
                'related_id' => $taskMap["project1_2"], // Foundation Construction task
                'related_type' => 'task',
                'is_read' => true,
                'read_at' => now()->subDays(11),
            ],
            [
                'user_id' => $contractor2->id,
                'title' => 'New Task Assigned',
                'message' => 'Engineer Two has assigned you the "Permit Acquisition" task.',
                'type' => 'task',
                'related_id' => $taskMap["project2_3"], // Permit Acquisition task
                'related_type' => 'task',
                'is_read' => false,
            ],
            [
                'user_id' => $owner->id,
                'title' => 'Budget Alert',
                'message' => 'The Commercial Office Building project is approaching 25% of its budget allocation.',
                'type' => 'budget',
                'related_id' => $project1->id, // Project 1
                'related_type' => 'project',
                'is_read' => false,
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::create($notification);
        }

        // Create a completed project for ratings
        $completedProject = Project::create([
            'name' => 'Small Office Renovation',
            'description' => 'Renovation of a small office space including new flooring, walls, and electrical',
            'location' => 'Downtown Business Center, Suite 300',
            'start_date' => now()->subMonths(3),
            'end_date' => now()->subDays(5),
            'budget' => 150000.00,
            'status' => 'completed',
            'owner_id' => $owner->id,
        ]);

        // Add project members to completed project
        ProjectMember::create([
            'project_id' => $completedProject->id,
            'user_id' => $owner->id,
            'role_id' => $ownerRole->id,
            'status' => 'active',
            'joined_at' => now()->subMonths(3),
        ]);

        ProjectMember::create([
            'project_id' => $completedProject->id,
            'user_id' => $engineer1->id,
            'role_id' => $engineerRole->id,
            'status' => 'active',
            'joined_at' => now()->subMonths(3),
        ]);

        ProjectMember::create([
            'project_id' => $completedProject->id,
            'user_id' => $contractor1->id,
            'role_id' => $contractorRole->id,
            'status' => 'active',
            'joined_at' => now()->subMonths(3),
        ]);

        // Create ratings for the completed project
        $ratings = [
            [
                'project_id' => $completedProject->id,
                'rated_by' => $owner->id,
                'rated_user_id' => $engineer1->id,
                'rating' => 5,
                'comment' => 'Excellent work on the design and oversight. Very professional and responsive.',
            ],
            [
                'project_id' => $completedProject->id,
                'rated_by' => $owner->id,
                'rated_user_id' => $contractor1->id,
                'rating' => 4,
                'comment' => 'Good quality work, completed on time. Some minor issues with cleanup.',
            ],
            [
                'project_id' => $completedProject->id,
                'rated_by' => $engineer1->id,
                'rated_user_id' => $contractor1->id,
                'rating' => 4,
                'comment' => 'Followed specifications well. Responsive to change requests.',
            ],
            [
                'project_id' => $completedProject->id,
                'rated_by' => $contractor1->id,
                'rated_user_id' => $engineer1->id,
                'rating' => 5,
                'comment' => 'Clear designs and specifications. Always available for questions.',
            ],
            [
                'project_id' => $completedProject->id,
                'rated_by' => $contractor1->id,
                'rated_user_id' => $owner->id,
                'rating' => 5,
                'comment' => 'Great client to work with. Clear requirements and prompt payments.',
            ],
        ];

        foreach ($ratings as $rating) {
            Rating::create($rating);
        }

        // Update average ratings
        $users = User::all();
        foreach ($users as $user) {
            $avgRating = Rating::where('rated_user_id', $user->id)->avg('rating');
            if ($avgRating) {
                $user->update(['average_rating' => $avgRating]);
            }
        }
    }
}
