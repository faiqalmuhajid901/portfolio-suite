<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'category',
        'client',
        'status',
        'start_date',
        'end_date',
        'image',
        'website_url',
        'description',
        'tags',
        'likes',
    ];

    protected $casts = [
        'tags' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}