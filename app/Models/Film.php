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

    protected $with = ['genres, actors, users'];

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
            'director',
            'run_time',
            'released',
            'imdb_id',
        ];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
