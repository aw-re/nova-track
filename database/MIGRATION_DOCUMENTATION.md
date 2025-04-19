# Database Migration Documentation

## Overview

This document details the database migrations implemented to fix errors in the Construction Project Management System (CPMS). These migrations address missing columns that were causing SQL errors in the application.

## Migrations Implemented

### 1. Add `created_by` Column to Tasks Table

**Migration File:** `2025_04_15_023000_add_created_by_to_tasks_table.php`

**Purpose:** This migration adds a `created_by` column to the `tasks` table to track which user created each task. The absence of this column was causing the following error:

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'created_by' in 'where clause'
```

**Schema Changes:**
- Added `created_by` column (bigint, unsigned, nullable) to the `tasks` table
- Added foreign key constraint referencing the `id` column in the `users` table
- Set the constraint to `ON DELETE SET NULL` to maintain data integrity if a user is deleted

### 2. Add `type` Column to Resources Table

**Migration File:** `2025_04_15_023001_add_type_to_resources_table.php`

**Purpose:** This migration adds a `type` column to the `resources` table to categorize resources. The absence of this column was causing the following error:

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'type' in 'where clause'
```

**Schema Changes:**
- Added `type` column (varchar, 255 characters) to the `resources` table
- Set default value to 'material' to ensure backward compatibility with existing records

## Manual SQL Migration

If you prefer to run the migrations manually or are experiencing issues with Laravel's migration system, you can use the SQL script provided at:

```
/database/sql/run_migrations.sql
```

This script contains the raw SQL commands to:
1. Create the migrations table if it doesn't exist
2. Record the migrations in the migrations table
3. Add the required columns with appropriate constraints

## How to Run Migrations

### Using Laravel's Migration System

```bash
php artisan migrate
```

### Using Manual SQL Script

You can run the SQL script directly in your database management tool or using the command line:

```bash
mysql -u username -p database_name < /path/to/run_migrations.sql
```

## Affected Controllers and Views

The following controllers and views have been updated to properly handle the new columns:

1. `App\Http\Controllers\Admin\ResourceController` - Updated to handle the `type` column
2. `App\Http\Controllers\Engineer\TaskController` - Already had proper handling of `created_by`
3. `App\Http\Controllers\Contractor\TaskController` - Updated to properly pass tasks to the view
4. Various view files - Updated with proper null checks to prevent property access errors

## Verification

After running the migrations, verify that:

1. The `tasks` table has a `created_by` column
2. The `resources` table has a `type` column
3. The application no longer shows SQL errors related to missing columns
4. The Engineer and Contractor interfaces work correctly without errors

## Troubleshooting

If you encounter issues after running the migrations:

1. Check that the migrations were recorded in the `migrations` table
2. Verify that the columns were added with the correct data types and constraints
3. Clear Laravel's cache with `php artisan cache:clear`
4. Restart your web server

For persistent issues, you may need to manually run the SQL commands in the `run_migrations.sql` file.
