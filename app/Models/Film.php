<?php

namespace App\Models;

use App\Enums\FilmStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Film extends Model
{
    /** @use HasFactory<\Database\Factories\FilmFactory> */
    use HasFactory;

    protected $casts =
        [
            'run_time' => 'int',
            'released' => 'int',
            'status' => FilmStatus::class,
        ];

    protected $hidden =
        [
            'created_at',
            'updated_at',
        ];

    protected $fillable =
        [
            'name',
            'poster_image',
            'preview_image',
            'background_image',
            'background_color',
            'video_link',
            'preview_video_link',
            'description',
            'run_time',
            'released',
            'imdb_id',
            'status',
        ];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class);
    }

    public function directors(): BelongsToMany
    {
        return $this->belongsToMany(Director::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'film_user', 'film_id', 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function scopeGenre($query, ?int $genreId)
    {
        return $genreId
            ? $query->whereHas('genres', fn ($q) => $q->whereKey($genreId))
            : $query;
    }

    public function scopeStatus($query, ?string $status)
    {
        if (!$status) {
            $status = FilmStatus::READY->value;
        }

        return $query->where('status', $status);
    }

    public function scopeWithRating($query)
    {
        return $query->withAvg('comments as rating', 'rating');
    }

    public function scopeSorting($query, string $field, string $direction)
    {
        if ($field === 'rating') {
            $query->withRating();
        }
        return $query->orderBy($field, $direction);
    }
}
