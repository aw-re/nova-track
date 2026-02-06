<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\TaskStatusEnum;
use App\Enums\TaskPriorityEnum;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'project_id',
        'created_by',
        'assigned_by',
        'assigned_to',
        'priority',
        'status',
        'start_date',
        'due_date',
        'completed_at',
        'estimated_hours',
        'actual_hours',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'status' => TaskStatusEnum::class,
        'priority' => TaskPriorityEnum::class,
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function updates()
    {
        return $this->hasMany(TaskUpdate::class);
    }

    public function resourceRequests()
    {
        return $this->hasMany(ResourceRequest::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

}
