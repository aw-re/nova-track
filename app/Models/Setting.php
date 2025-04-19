<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Cast value to appropriate type based on key.
     *
     * @param  string  $value
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        // Cast boolean values
        if (in_array($this->key, [
            'enable_email_notifications',
            'enable_welcome_email',
            'enable_two_factor',
            'force_password_change'
        ])) {
            return (bool) $value;
        }

        // Cast numeric values
        if (in_array($this->key, [
            'session_timeout',
            'max_login_attempts',
            'max_file_size'
        ])) {
            return (int) $value;
        }

        return $value;
    }
}
