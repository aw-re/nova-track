<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\ResourceController as AdminResourceController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Admin\FileController as AdminFileController;
use App\Http\Controllers\Admin\RatingController as AdminRatingController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;

use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\ProjectController as OwnerProjectController;
use App\Http\Controllers\Owner\TaskController as OwnerTaskController;
use App\Http\Controllers\Owner\ReportController as OwnerReportController;
use App\Http\Controllers\Owner\ResourceRequestController as OwnerResourceRequestController;
use App\Http\Controllers\Owner\FileController as OwnerFileController;

use App\Http\Controllers\Engineer\DashboardController as EngineerDashboardController;
use App\Http\Controllers\Engineer\TaskController as EngineerTaskController;
use App\Http\Controllers\Engineer\ReportController as EngineerReportController;
use App\Http\Controllers\Engineer\ResourceRequestController as EngineerResourceRequestController;
use App\Http\Controllers\Engineer\FileController as EngineerFileController;

use App\Http\Controllers\Contractor\DashboardController as ContractorDashboardController;
use App\Http\Controllers\Contractor\TaskController as ContractorTaskController;
use App\Http\Controllers\Contractor\ResourceRequestController as ContractorResourceRequestController;
use App\Http\Controllers\Contractor\FileController as ContractorFileController;

use App\Http\Controllers\Settings\SettingsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


use App\Http\Controllers\Owner\TaskUpdateController;





Route::prefix('admin')->group(function() {
    Route::resource('reports', \App\Http\Controllers\Admin\ReportController::class);
    
    // إضافة مسارات الموافقة والرفض
    Route::post('reports/{report}/approve', [\App\Http\Controllers\Admin\ReportController::class, 'approve'])
         ->name('admin.reports.approve');
    
    Route::post('reports/{report}/reject', [\App\Http\Controllers\Admin\ReportController::class, 'reject'])
         ->name('admin.reports.reject');
});



Route::get('/admin/tasks', [TaskController::class, 'index'])
     ->name('admin.tasks.index');

Route::post('tasks/{task}/updates', [TaskUpdateController::class, 'store'])
     ->name('owner.tasks.updates.store');


Route::post('tasks/{task}/updates', [TaskUpdateController::class, 'store'])
     ->name('owner.tasks.updates.store');



Route::put('owner/reports/{report}/approve', [ReportController::class, 'approve'])
     ->name('owner.reports.approve');




Route::post('reports/{report}/comments', [ReportCommentController::class, 'store'])
     ->name('owner.reports.comments.store');



// استبدل PUT بـ POST
Route::post('owner/resource-requests/{resourceRequest}/approve', [ResourceRequestController::class, 'approve'])
     ->name('owner.resource-requests.approve');





Route::get('resource-requests/{resourceRequest}', [ResourceRequestController::class, 'show'])
     ->name('owner.resource-requests.show');




Route::get('/resource-requests', [ResourceRequestController::class, 'index'])
     ->name('owner.resource-requests.index');




Route::put('/tasks/{task}/update-status', [TaskController::class, 'updateStatus'])
    ->name('owner.tasks.update-status');


Route::post('tasks/{task}/assign', [TaskController::class, 'assignTask'])
     ->name('owner.tasks.assign')
     ->middleware(['auth', 'role:project_owner']);



Route::post('tasks/{task}/assign', [TaskController::class, 'assign'])
    ->name('owner.tasks.assign');





Route::put('/reports/{report}/approve', [ReportController::class, 'approve'])
    ->name('owner.reports.approve');
// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Redirect authenticated users based on role
Route::get('/dashboard', function() {
    if (auth()->user()->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif (auth()->user()->hasRole('project_owner')) {
        return redirect()->route('owner.dashboard');
    } elseif (auth()->user()->hasRole('engineer')) {
        return redirect()->route('engineer.dashboard');
    } elseif (auth()->user()->hasRole('contractor')) {
        return redirect()->route('contractor.dashboard');
    }
    return redirect()->route('login');
})->middleware('auth')->name('dashboard');

// Settings Routes (accessible to all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.update-profile');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.update-password');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::resource('users', AdminUserController::class);
    
    // Role Management
    Route::resource('roles', AdminRoleController::class);
    
    // Project Management
    Route::resource('projects', AdminProjectController::class);
    
    // Resource Management
    Route::resource('resources', AdminResourceController::class);
    
    // Report Management
    Route::resource('reports', AdminReportController::class);
    
    // Activity Logs
    Route::get('/activity-logs', [AdminActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/{log}', [AdminActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::post('/activity-logs/clear', [AdminActivityLogController::class, 'clearAll'])->name('activity-logs.clear');
    Route::get('/activity-logs/export', [AdminActivityLogController::class, 'export'])->name('activity-logs.export');
    
    // Notifications
    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-as-read', [AdminNotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::delete('/notifications/{notification}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');
    
    // Task Management
    Route::resource('tasks', AdminTaskController::class);
    
    // File Management
    Route::resource('files', AdminFileController::class);
    Route::get('/files/{file}/download', [AdminFileController::class, 'download'])->name('files.download');
    
    // Rating Management
    Route::resource('ratings', AdminRatingController::class);
    
    // Settings
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');
});

// Project Owner Routes
Route::prefix('owner')->middleware(['auth', 'role:project_owner'])->name('owner.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
    
    // Project Management
    Route::resource('projects', OwnerProjectController::class);
    
    // Project Members Management
    Route::post('/projects/{project}/invite', [OwnerProjectController::class, 'inviteMembers'])->name('projects.invite');
    Route::delete('/projects/{project}/members/{user}', [OwnerProjectController::class, 'removeMember'])->name('projects.members.remove');
    
    // Task Management
    Route::resource('tasks', OwnerTaskController::class);
    Route::post('/tasks/{task}/assign', [OwnerTaskController::class, 'assignTask'])->name('tasks.assign');
    
    // Report Management
    Route::resource('reports', OwnerReportController::class);
    Route::post('/reports/{report}/approve', [OwnerReportController::class, 'approve'])->name('reports.approve');
    Route::post('/reports/{report}/reject', [OwnerReportController::class, 'reject'])->name('reports.reject');
    
    // Resource Request Management
    Route::resource('resource-requests', OwnerResourceRequestController::class);
    Route::post('/resource-requests/{resourceRequest}/approve', [OwnerResourceRequestController::class, 'approve'])->name('resource-requests.approve');
    Route::post('/resource-requests/{resourceRequest}/reject', [OwnerResourceRequestController::class, 'reject'])->name('resource-requests.reject');
    
    // File Management
    Route::resource('files', OwnerFileController::class);
    Route::get('/files/{file}/download', [OwnerFileController::class, 'download'])->name('files.download');
    
    // Notifications
    Route::get('/notifications', [OwnerDashboardController::class, 'notifications'])->name('notifications.index');
    Route::post('/notifications/mark-as-read', [OwnerDashboardController::class, 'markNotificationsAsRead'])->name('notifications.mark-as-read');
    
    // Profile
    Route::get('/profile/edit', [OwnerDashboardController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [OwnerDashboardController::class, 'updateProfile'])->name('profile.update');
});

// Engineer Routes
Route::prefix('engineer')->middleware(['auth', 'role:engineer'])->name('engineer.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [EngineerDashboardController::class, 'index'])->name('dashboard');
    
    // Project View
    Route::get('/projects', [EngineerDashboardController::class, 'projects'])->name('projects.index');
    Route::get('/projects/{project}', [EngineerDashboardController::class, 'showProject'])->name('projects.show');
    
    // Project Invitations
    Route::get('/invitations', [EngineerDashboardController::class, 'invitations'])->name('invitations.index');
    Route::post('/invitations/{invitation}/accept', [EngineerDashboardController::class, 'acceptInvitation'])->name('invitations.accept');
    Route::post('/invitations/{invitation}/reject', [EngineerDashboardController::class, 'rejectInvitation'])->name('invitations.reject');
    
    // Task Management
    Route::resource('tasks', EngineerTaskController::class);
    Route::post('/tasks/{task}/assign', [EngineerTaskController::class, 'assignTask'])->name('tasks.assign');
    Route::post('/tasks/{task}/update-status', [EngineerTaskController::class, 'updateStatus'])->name('tasks.update-status');
    
    // Report Management
    Route::resource('reports', EngineerReportController::class);
    
    // Resource Request Management
    Route::resource('resource-requests', EngineerResourceRequestController::class);
    
    // File Management
    Route::resource('files', EngineerFileController::class);
    Route::get('/files/{file}/download', [EngineerFileController::class, 'download'])->name('files.download');
    
    // Notifications
    Route::get('/notifications', [EngineerDashboardController::class, 'notifications'])->name('notifications.index');
    Route::post('/notifications/mark-as-read', [EngineerDashboardController::class, 'markNotificationsAsRead'])->name('notifications.mark-as-read');
    
    // Profile
    Route::get('/profile/edit', [EngineerDashboardController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [EngineerDashboardController::class, 'updateProfile'])->name('profile.update');
});

// Contractor Routes
Route::prefix('contractor')->middleware(['auth', 'role:contractor'])->name('contractor.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [ContractorDashboardController::class, 'index'])->name('dashboard');
    
    // Project View
    Route::get('/projects', [ContractorDashboardController::class, 'projects'])->name('projects.index');
    Route::get('/projects/{project}', [ContractorDashboardController::class, 'showProject'])->name('projects.show');
    
    // Project Invitations
    Route::get('/invitations', [ContractorDashboardController::class, 'invitations'])->name('invitations.index');
    Route::post('/invitations/{invitation}/accept', [ContractorDashboardController::class, 'acceptInvitation'])->name('invitations.accept');
    Route::post('/invitations/{invitation}/reject', [ContractorDashboardController::class, 'rejectInvitation'])->name('invitations.reject');
    
    // Task Management
    Route::resource('tasks', ContractorTaskController::class)->except(['create', 'store', 'destroy']);
    Route::post('/tasks/{task}/start', [ContractorTaskController::class, 'startTask'])->name('tasks.start');
    Route::post('/tasks/{task}/complete', [ContractorTaskController::class, 'completeTask'])->name('tasks.complete');
    Route::post('/tasks/{task}/update-progress', [ContractorTaskController::class, 'updateProgress'])->name('tasks.update-progress');
    
    // Resource Request Management
    Route::resource('resource-requests', ContractorResourceRequestController::class);
    
    // File Management
    Route::resource('files', ContractorFileController::class);
    Route::get('/files/{file}/download', [ContractorFileController::class, 'download'])->name('files.download');
    
    // Notifications
    Route::get('/notifications', [ContractorDashboardController::class, 'notifications'])->name('notifications.index');
    Route::post('/notifications/mark-as-read', [ContractorDashboardController::class, 'markNotificationsAsRead'])->name('notifications.mark-as-read');
    
    // Profile
    Route::get('/profile/edit', [ContractorDashboardController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [ContractorDashboardController::class, 'updateProfile'])->name('profile.update');
});
