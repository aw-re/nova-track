# NovaTrack Changelog

## [2.0.0] - 2026-02-06

### 🔧 Critical Fixes

#### Route Fixes
- Fixed duplicate admin report routes that lacked middleware protection
- Added `approve` and `reject` routes for reports within protected admin group

#### Enum Consistency
- Fixed `Contractor\TaskController` to use `TaskStatusEnum` instead of raw strings
- All status comparisons now use proper Enum values

#### Model Fixes
- Removed erroneous `submittedBy()` relationship from `Report` model
- Removed duplicate `projectMembers()` method from `Project` model
- Removed invalid `rejectedBy()` and `tasks()` relationships from `ResourceRequest` model
- Updated `TaskUpdate` model with status tracking fields

#### Policy Registration
- Registered `TaskPolicy` in `AuthServiceProvider`
- Created and registered `ProjectPolicy`, `ReportPolicy`, `ResourceRequestPolicy`

### ✨ New Features

#### New Enums
- `ProjectStatusEnum` - Project lifecycle states
- `ReportStatusEnum` - Report workflow states
- `ReportTypeEnum` - Report type classification
- `ResourceRequestStatusEnum` - Resource request states
- `UserRoleEnum` - User role classification

#### New Policies
- `ProjectPolicy` - Authorization for project operations
- `ReportPolicy` - Authorization for report operations with approval workflow
- `ResourceRequestPolicy` - Authorization for resource request operations

#### New Traits
- `ManagesProfile` - Reusable profile management functionality
- `ManagesNotifications` - Reusable notification management

#### New Form Requests
- `StoreProjectRequest` - Validation for project creation
- `UpdateProjectRequest` - Validation for project updates
- `StoreReportRequest` - Validation for report creation
- `StoreResourceRequestRequest` - Validation for resource requests

### 🗄️ Database Updates

#### New Tables in `final_clean_install.sql`
- `task_updates` - Track task status changes and comments
- `files` - File management for projects and tasks
- `project_invitations` - Project invitation system
- `comments` - Polymorphic comments system
- `ratings` - User rating system
- `notifications` - Laravel standard notifications

#### Schema Improvements
- Added `rejection_reason` to `reports` table
- Added `pending` status to report status enum
- Added all necessary foreign key constraints

### 🌐 Localization

#### Expanded Translation Files
- `en/app.php` - 200+ English translations
- `ar/app.php` - 200+ Arabic translations
- `en/enums.php` - All enum translations
- `ar/enums.php` - All enum translations (Arabic)
- `en/messages.php` - Comprehensive success/error messages
- `ar/messages.php` - Comprehensive success/error messages (Arabic)
- `ar/validation.php` - Complete Arabic validation messages

### 🎨 View Improvements
- Fixed `owner/dashboard.blade.php` to use proper translations
- Updated project status display to use Enum labels and colors
- Improved date formatting using Carbon

---

## [1.0.0] - Previous Version

Initial release with:
- Basic CRUD for Projects, Tasks, Reports
- Role-based authentication
- Glassmorphism UI design
- Bilingual support (En/Ar)
