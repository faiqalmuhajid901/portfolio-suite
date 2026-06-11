<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'role',
        'bio',
        'avatar',
        'hero_badge',
        'hero_title',
        'hero_description',
        'hero_background',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}