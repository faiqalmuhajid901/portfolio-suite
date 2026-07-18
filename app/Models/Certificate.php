<?php

namespace App\Models;

use App\Support\PublicPortfolioCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'issuer',
        'issued_at',
        'pdf_path',
        'description',
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'is_visible' => 'boolean',
        ];
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    protected static function booted(): void
    {
        static::saved(
            static function (Certificate $certificate): void {
                PublicPortfolioCache::forgetCertificates();
            }
        );

        static::deleted(
            static function (Certificate $certificate): void {
                PublicPortfolioCache::forgetCertificates();
            }
        );
    }
}
