<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Education extends Model
{
    /**
     * Nama tabel harus ditetapkan secara eksplisit.
     *
     * Tanpa properti ini, Eloquent pada aplikasi Anda
     * menghasilkan query ke tabel "education".
     */
    protected $table = 'educations';

    protected $fillable = [
        'profile_id',
        'level',
        'institution',
        'major',
        'gpa',
        'start_year',
        'end_year',
        'status',
        'description',
        'sort_order',
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'gpa' => 'decimal:2',
            'start_year' => 'integer',
            'end_year' => 'integer',
            'sort_order' => 'integer',
            'is_visible' => 'boolean',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
