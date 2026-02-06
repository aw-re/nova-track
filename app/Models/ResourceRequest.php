<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ResourceRequestStatusEnum;

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
        'requested_date',
        'required_date',
        'required_by',
        'description',
        'document_path',
        'status',
        'notes',
        'approved_at',
        'delivered_at',
        'rejection_reason',
    ];

    protected $casts = [
        'required_by' => 'date',
        'approved_at' => 'datetime',
        'delivered_at' => 'datetime',
        'quantity' => 'decimal:2',
        'status' => ResourceRequestStatusEnum::class,
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

