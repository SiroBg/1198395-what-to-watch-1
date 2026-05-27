<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promo extends Model
{
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory;
    protected $with = ['users'];

    protected $hidden =
        [
            'created_at',
            'updated_at',
        ];

    protected $fillable =
        [
            'film_id',
        ];

    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }
}
