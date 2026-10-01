<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalSetting extends Model
{
    protected $fillable = ['site_name', 'support_email', 'registration_open', 'job_posting_open', 'applications_open'];

    protected $attributes = [
        'site_name' => 'JobPortal',
        'registration_open' => true,
        'job_posting_open' => true,
        'applications_open' => true,
    ];

    protected function casts(): array
    {
        return ['registration_open' => 'boolean', 'job_posting_open' => 'boolean', 'applications_open' => 'boolean'];
    }

    public static function current(): self
    {
        $settings = static::find(1) ?? new static;
        $settings->id = 1;

        return $settings;
    }
}
