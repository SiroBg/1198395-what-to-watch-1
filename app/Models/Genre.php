<?php

namespace App\Models;

use Database\Factories\GenreFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @psalm-api
 */
class Genre extends Model
{
    /** @use HasFactory<GenreFactory> */
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
     * Возвращает список фильмов с этим жанром.
     */
    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class)->withTimestamps();
    }
}
