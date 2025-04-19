# CPMS Error Fixes Documentation

## Overview

This document provides detailed information about the errors that were identified in the Construction Project Management System (CPMS) and the fixes that have been implemented to resolve them. The fixes address issues in both the engineer and contractor interfaces, ensuring the system operates normally without errors.

## Identified Errors

The following errors were identified in the system:

1. **Call to undefined relationship [project] on model [App\Models\Resource]**
   - Location: Admin ResourceController
   - Error: The Resource model was missing the project relationship method.

2. **Attempt to read property "project" on int**
   - Location: Engineer and Contractor resource-requests views
   - Error: The views were trying to access properties on project IDs instead of project objects.

3. **Undefined variable $tasks**
   - Location: Engineer and Contractor tasks views
   - Error: The controllers were not properly passing the tasks variable to the views.

4. **SQLSTATE[42S22]: Column not found: 1054 Unknown column 'created_by' in 'where clause'**
   - Location: Engineer TaskController
   - Error: The 'created_by' column was missing from the tasks table.

5. **SQLSTATE[42S22]: Column not found: 1054 Unknown column 'type' in 'where clause'**
   - Location: Admin ResourceController
   - Error: The 'type' column was missing from the resources table.

## Implemented Fixes

### 1. Database Schema Fixes

#### Added Missing Columns to Tables

Created migration files to add missing columns to the database tables:

```php
// 2025_04_15_023000_add_created_by_to_tasks_table.php
public function up()
{
    Schema::table('tasks', function (Blueprint $table) {
        if (!Schema::hasColumn('tasks', 'created_by')) {
            $table->unsignedBigInteger('created_by')->nullable()->after('id');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        }
    });
}

// 2025_04_15_023001_add_type_to_resources_table.php
public function up()
{
    Schema::table('resources', function (Blueprint $table) {
        if (!Schema::hasColumn('resources', 'type')) {
            $table->string('type')->nullable()->after('description');
        }
    });
}
```

#### SQL Script for Manual Migration

For users who prefer to run SQL directly, we've provided a SQL script:

```sql
-- Add created_by column to tasks table if it doesn't exist
ALTER TABLE tasks ADD COLUMN IF NOT EXISTS created_by BIGINT UNSIGNED NULL AFTER id;
ALTER TABLE tasks ADD CONSTRAINT IF NOT EXISTS tasks_created_by_foreign FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;

-- Add type column to resources table if it doesn't exist
ALTER TABLE resources ADD COLUMN IF NOT EXISTS type VARCHAR(255) NULL AFTER description;
```

### 2. Model Relationship Fixes

#### Resource Model

Added the missing project relationship to the Resource model:

```php
/**
 * Get the project that owns the resource.
 */
public function project(): BelongsTo
{
    return $this->belongsTo(Project::class);
}
```

#### Task Model

Updated the Task model to include the createdBy relationship:

```php
/**
 * Get the user who created the task.
 */
public function createdBy()
{
    return $this->belongsTo(User::class, 'created_by');
}
```

### 3. Controller Fixes

#### ResourceController

Updated the Admin ResourceController to properly handle the 'type' column:

```php
// Get counts for different resource types
$materialCount = Resource::where('type', 'material')->count();
$equipmentCount = Resource::where('type', 'equipment')->count();
$laborCount = Resource::where('type', 'labor')->count();
```

#### Engineer and Contractor TaskControllers

Modified both controllers to provide both a collection and paginated version of tasks:

```php
// Get all tasks as a collection (not paginated)
$tasks = Task::with(['project', 'createdBy'])
    ->where('assigned_to', Auth::id())
    ->orderBy('due_date')
    ->get();
    
// For pagination in the main view
$paginatedTasks = Task::with(['project', 'createdBy'])
    ->where('assigned_to', Auth::id())
    ->orderBy('due_date')
    ->paginate(10);
    
return view('engineer.tasks.index', compact('tasks', 'paginatedTasks'));
```

#### ResourceRequestController

Updated the ResourceRequestController to provide both a collection and paginated version of requests:

```php
// Get all requests as a collection for filtering in the view
$allRequests = ResourceRequest::with(['project'])
    ->where('requested_by', Auth::id())
    ->orderBy('created_at', 'desc')
    ->get();
    
// Paginated versions for different tabs
$pendingRequests = ResourceRequest::with(['project'])
    ->where('requested_by', Auth::id())
    ->whereIn('status', ['pending', 'approved'])
    ->orderBy('created_at', 'desc')
    ->paginate(10, ['*'], 'pending_page');
```

### 4. View Fixes

#### Comprehensive Null Checks

Added proper null checks in all views to prevent "Attempt to read property on null" errors:

```php
{{ isset($task->project) && is_object($task->project) ? $task->project->name : 'N/A' }}
```

#### Empty Collection Handling

Added proper handling for empty collections:

```php
@forelse($paginatedTasks ?? [] as $task)
    <!-- Task display code -->
@empty
    <tr>
        <td colspan="6" class="text-center">No tasks found.</td>
    </tr>
@endforelse
```

#### Pagination Handling

Added proper checks before displaying pagination links:

```php
@if(isset($paginatedTasks) && $paginatedTasks->hasPages())
    <div class="mt-4">
        {{ $paginatedTasks->links() }}
    </div>
@endif
```

## Verification Steps

To verify that all fixes have been properly implemented, follow these steps:

### 1. Database Migration Verification

1. Run the migrations:
   ```
   php artisan migrate
   ```
   
2. Or execute the SQL script directly in your database management tool.

3. Verify the columns exist:
   ```sql
   DESCRIBE tasks;
   DESCRIBE resources;
   ```

### 2. Engineer Interface Verification

1. Log in as an engineer user.
2. Navigate to the Tasks page.
   - Verify no "Undefined variable $tasks" error appears.
   - Verify all tasks are displayed correctly with project names.
3. Navigate to the Resource Requests page.
   - Verify no "Attempt to read property 'project' on int" error appears.
   - Verify all resource requests are displayed correctly with project names.

### 3. Contractor Interface Verification

1. Log in as a contractor user.
2. Navigate to the Tasks page.
   - Verify no "Undefined variable $tasks" error appears.
   - Verify all tasks are displayed correctly with project names.
3. Navigate to the Resource Requests page.
   - Verify no "Attempt to read property 'project' on int" error appears.
   - Verify all resource requests are displayed correctly with project names.
4. Navigate to the Files page.
   - Verify no "Attempt to read property 'name' on null" error appears.
   - Verify all files are displayed correctly with project names.

### 4. Admin Interface Verification

1. Log in as an admin user.
2. Navigate to the Resources page.
   - Verify no "Call to undefined relationship [project]" error appears.
   - Verify the resource counts by type are displayed correctly.
   - Verify all resources are displayed correctly with project names.

## Conclusion

The implemented fixes address all the identified errors in the CPMS system. The fixes include database schema updates, model relationship additions, controller modifications, and view improvements with proper null checks and error handling. These changes ensure the system operates normally without errors across all user interfaces.

If you encounter any issues or have questions about the implemented fixes, please refer to the code comments or contact the development team for assistance.
