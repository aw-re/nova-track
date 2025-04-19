# Construction Project Management System (CPMS)

A comprehensive Laravel-based system for managing construction projects with multiple user roles, task management, resource tracking, and reporting features.

## Features

- **Multi-Role User System**:
  - Admin: System management and oversight
  - Project Owner: Project creation and management
  - Engineer: Technical oversight and task assignment
  - Contractor: Task execution and resource requests

- **Project Management**:
  - Create, edit, and track construction projects
  - Assign team members to projects
  - Monitor project progress and status

- **Task Management**:
  - Create and assign tasks to team members
  - Track task status and completion
  - Update task progress

- **Resource Management**:
  - Request construction materials and equipment
  - Approve or reject resource requests
  - Track resource allocation

- **Reporting System**:
  - Submit technical and progress reports
  - Review and approve reports
  - Generate project status reports

- **File Management**:
  - Upload and download project files
  - Organize files by project
  - Track file versions

- **Notifications & Activity Logs**:
  - Real-time notifications for important events
  - Comprehensive activity tracking
  - Audit trail for all system actions

## System Requirements

- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer
- Node.js and NPM (for asset compilation)
- XAMPP, WAMP, LAMP, or similar local development environment

## Installation

1. Clone the repository or extract the ZIP file to your local environment

2. Navigate to the project directory:
   ```
   cd cpms
   ```

3. Install PHP dependencies:
   ```
   composer install
   ```

4. Copy the environment file:
   ```
   cp .env.example .env
   ```

5. Generate application key:
   ```
   php artisan key:generate
   ```

6. Configure your database in the `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cpms
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. Run database migrations and seed with test data:
   ```
   php artisan migrate --seed
   ```

8. Create test users for each role:
   ```
   php artisan db:seed --class=TestUserSeeder
   ```

9. Start the development server:
   ```
   php artisan serve
   ```

10. Access the application at `http://localhost:8000`

## Test Accounts

The system comes with pre-configured test accounts for each user role:

- **Admin**:
  - Email: admin@example.com
  - Password: password

- **Project Owner**:
  - Email: owner@example.com
  - Password: password

- **Engineer**:
  - Email: engineer@example.com
  - Password: password

- **Contractor**:
  - Email: contractor@example.com
  - Password: password

## System Structure

### Models

- **User**: Manages user accounts with role-based permissions
- **Role**: Defines user roles and permissions
- **Project**: Manages construction project details
- **Task**: Handles task assignments and tracking
- **Report**: Manages technical and progress reports
- **ResourceRequest**: Handles material and equipment requests
- **File**: Manages document uploads and downloads
- **Notification**: Handles system notifications
- **ActivityLog**: Tracks user actions in the system

### Controllers

- **Admin Controllers**: Manage system-wide settings and users
- **Owner Controllers**: Handle project creation and management
- **Engineer Controllers**: Manage technical aspects and task assignment
- **Contractor Controllers**: Handle task execution and updates

### Views

- **Layouts**: Common page layouts and components
- **Admin Views**: System management interfaces
- **Owner Views**: Project management interfaces
- **Engineer Views**: Technical management interfaces
- **Contractor Views**: Task execution interfaces

## Workflow Examples

### Project Creation Workflow

1. Project Owner logs in
2. Creates a new project with details
3. Invites Engineers and Contractors
4. Sets up initial tasks
5. Monitors progress

### Task Management Workflow

1. Engineer creates tasks
2. Assigns tasks to Contractors
3. Contractor updates task progress
4. Engineer reviews and approves completion
5. Project Owner monitors overall progress

### Resource Request Workflow

1. Contractor identifies needed resources
2. Submits resource request
3. Engineer reviews and recommends
4. Project Owner approves or rejects
5. Resources are allocated to the project

## Customization

The system is built with Laravel, making it highly customizable:

- Add new user roles by modifying the Role model and seeders
- Extend project attributes in the Project model
- Customize workflows by modifying controllers
- Add new features by creating new models, controllers, and views

## Troubleshooting

- **Database Connection Issues**: Verify your database credentials in the `.env` file
- **Missing Dependencies**: Run `composer install` to ensure all packages are installed
- **Permission Issues**: Ensure storage and bootstrap/cache directories are writable
- **Role Assignment Problems**: Check the role_user pivot table for proper associations

## License

This project is licensed under the MIT License - see the LICENSE file for details.
