<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_update_id',
        'image_path',
        'caption',
    ];

    public function taskUpdate()
    {
        return $this->belongsTo(TaskUpdate::class);
    }
}
