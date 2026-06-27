<?php

namespace App\Models;

use Database\Factories\DirectorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @psalm-api
 */
class Director extends Model
{
    /** @use HasFactory<DirectorFactory> */
    use HasFactory;

    protected $hidden
        = [
            'created_at',
            'updated_at',
        ];

    protected $fillable
        = [
            'name',
        ];

    /**
     * Возвращает список фильмов, снятых режиссёром.
     *
     * @return BelongsToMany
     */
    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class)->withTimestamps();
    }
}
