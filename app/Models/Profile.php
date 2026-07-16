<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'birth_date',
        'domicile',
        'public_email',
        'professional_status',
        'work_preference',
        'about_title',
        'about_description',
        'linkedin_url',
        'github_url',
        'cv_url',
        'languages',
        'current_focus',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'languages' => 'array',
            'current_focus' => 'array',
            'is_public' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function educations(): HasMany
    {
        return $this->hasMany(Education::class)
            ->orderBy('sort_order')
            ->orderByDesc('end_year')
            ->orderByDesc('start_year');
    }
}
