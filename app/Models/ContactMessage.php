<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    /**
     * Services offered in the contact form. Single source of truth for the
     * form options, request validation and the admin filter.
     */
    public const SERVICES = [
        'System Integrator',
        'Software & App Development',
        'IoT Solution & Surveillance Camera',
        'Professional Integrated Display Solution',
        'Hardware Service & Maintenance',
        'Lainnya',
    ];

    protected $fillable = [
        'name',
        'email',
        'company',
        'service',
        'message',
        'ip_address',
        'user_agent',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }
}
