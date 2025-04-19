<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'task_id',
        'resource_id',
        'requested_by',
        'approved_by',
        'resource_type',
        'resource_name',
        'quantity',
        'unit',
        'required_by',
        'description',
        'document_path',
        'status',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'required_by' => 'date',
        'approved_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
        'quantity' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
    
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    public function related_tasks()
    {
        return $this->hasMany(Task::class); // أو أي علاقة أخرى حسب هيكل DB
    }
    public function tasks()
{
    return $this->hasMany(Task::class, 'project_id', 'project_id');
}
}
