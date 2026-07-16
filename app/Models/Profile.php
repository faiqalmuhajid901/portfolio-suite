<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profile extends Model
{
    /**
     * Field yang boleh diisi melalui create() dan update().
     *
     * Field About Me wajib berada di sini agar data dari
     * AboutEditor tidak dibuang oleh mass-assignment protection.
     */
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

    /**
     * Mengubah data database menjadi tipe PHP yang sesuai.
     */
    protected function casts(): array
    {
        return [
            /*
             * Wajib agar:
             *
             * $profile->birth_date->age
             *
             * dapat digunakan. Tanpa cast ini, birth_date
             * hanya berupa string dari PostgreSQL.
             */
            'birth_date' => 'date',

            /*
             * Wajib agar JSON PostgreSQL dikembalikan
             * sebagai array PHP.
             */
            'languages' => 'array',
            'current_focus' => 'array',

            'is_public' => 'boolean',
        ];
    }

    /**
     * User pemilik profil.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Seluruh riwayat pendidikan milik profil.
     */
    public function educations(): HasMany
    {
        return $this->hasMany(Education::class)
            ->orderBy('sort_order')
            ->orderByDesc('end_year')
            ->orderByDesc('start_year');
    }
}
