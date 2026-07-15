<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioVisit extends Model
{
    protected $fillable = [
        'visitor_id',
        'session_id',
        'ip_hash',
        'path',
        'route_name',
        'referrer_host',
        'device_type',
        'browser',
        'operating_system',
        'country_code',
        'region',
        'city',
        'last_seen_at',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];
}
