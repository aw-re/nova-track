<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'company',
        'profile_image',
        'average_rating',
        'role',
    ];
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'average_rating' => 'decimal:2',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function ownedProjects()
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    public function projectMemberships()
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'assigned_by');
    }

    public function taskUpdates()
    {
        return $this->hasMany(TaskUpdate::class);
    }

    public function resourceRequests()
    {
        return $this->hasMany(ResourceRequest::class, 'requested_by');
    }

    public function approvedResourceRequests()
    {
        return $this->hasMany(ResourceRequest::class, 'approved_by');
    }

    public function uploadedFiles()
    {
        return $this->hasMany(File::class, 'uploaded_by');
    }

    public function createdReports()
    {
        return $this->hasMany(Report::class, 'created_by');
    }

    public function approvedReports()
    {
        return $this->hasMany(Report::class, 'approved_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function givenRatings()
    {
        return $this->hasMany(Rating::class, 'rated_by');
    }

    public function receivedRatings()
    {
        return $this->hasMany(Rating::class, 'rated_user_id');
    }

    /**
     * Get all project invitations for the user.
     */
    public function projectInvitations()
    {
        return $this->hasMany(ProjectInvitation::class, 'user_id');
    }

    /**
     * Get all project invitations sent by the user.
     */
    public function sentProjectInvitations()
    {
        return $this->hasMany(ProjectInvitation::class, 'invited_by');
    }

    public function hasRole($roleName)
    {
        // Check direct role column first
        if ($this->role === $roleName) {
            return true;
        }
        
        // Fall back to many-to-many relationship
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin' || $this->hasRole('admin');
    }

    /**
     * Check if the user is a project owner.
     *
     * @return bool
     */
    public function isProjectOwner()
    {
        return $this->role === 'owner' || $this->hasRole('project_owner');
    }

    /**
     * Check if the user is an engineer.
     *
     * @return bool
     */
    public function isEngineer()
    {
        return $this->role === 'engineer' || $this->hasRole('engineer');
    }

    /**
     * Check if the user is a contractor.
     *
     * @return bool
     */
    public function isContractor()
    {
        return $this->role === 'contractor' || $this->hasRole('contractor');
    }

    /**
     * Get all projects the user is a member of.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_members', 'user_id', 'project_id');
    }
}
