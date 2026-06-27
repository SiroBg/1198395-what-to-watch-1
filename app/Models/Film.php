<?php

namespace App\Models;

use App\Enums\FilmStatus;
use Database\Factories\FilmFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @psalm-api
 *
 * @property int                        $id
 * @property string                     $name
 * @property float|int|null             $rating
 * @property int|null                   $scores_count
 * @property bool|int|null              $is_favorite
 * @property-read Collection|Director[] $directors
 * @property-read Collection|Actor[]    $actors
 * @property-read Collection|Genre[]    $genres
 */
class Film extends Model
{
    /** @use HasFactory<FilmFactory> */
    use HasFactory;

    protected $casts
        = [
            'run_time' => 'int',
            'released' => 'int',
            'status'   => FilmStatus::class,
        ];

    protected $hidden
        = [
            'created_at',
            'updated_at',
        ];

    protected $fillable
        = [
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

    /**
     * Возвращает жанры фильма.
     *
     * @return BelongsToMany
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class)->withTimestamps();
    }

    /**
     * Возвращает список актёров, снимавшихся в фильме.
     *
     * @return BelongsToMany
     */
    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class)->withTimestamps();
    }

    /**
     * Возвращает список режиссёров фильма.
     *
     * @return BelongsToMany
     */
    public function directors(): BelongsToMany
    {
        return $this->belongsToMany(Director::class)->withTimestamps();
    }

    /**
     * Возвращает список пользователей, добавивших фильм в избранное.
     *
     * @return BelongsToMany
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'film_user',
            'film_id',
            'user_id'
        )->withTimestamps();
    }

    /**
     * Возвращает отзывы к фильму.
     *
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Добавляет фильтр жанра к запросу.
     *
     * @param          $query
     * @param int|null $genreId Id Жанра.
     *
     * @return mixed
     */
    public function scopeGenre(
        $query,
        ?int $genreId
    ): mixed {
        return $genreId
            ? $query->whereHas('genres', fn($q) => $q->whereKey($genreId))
            : $query;
    }

    /**
     * Добавляет к запросу поле is_favorite (добавлен ли фильм в избранное
     * пользователем).
     *
     * @param          $query
     * @param int|null $userId Id пользователя.
     *
     * @return mixed
     */
    public function scopeWithIsFavorite($query, ?int $userId): mixed
    {
        if (!$userId) {
            return $query->selectRaw('0 as is_favorite');
        }

        return $query->withExists([
            'favoritedBy as is_favorite' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }
        ]);
    }

    /**
     * Добавляет фильтр по статусу фильма.
     *
     * @param             $query
     * @param string|null $status Статус.
     *
     * @return mixed
     */
    public function scopeStatus($query, ?string $status): mixed
    {
        if (!$status) {
            $status = FilmStatus::READY->value;
        }

        return $query->where('status', $status);
    }

    /**
     * Добавляет к запросу поле rating (среднее значение по всем отзывам).
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeWithRating($query): mixed
    {
        return $query->withAvg('comments as rating', 'rating');
    }

    /**
     * Добавляет фильтр сортировки по полю и направлению.
     *
     * @param        $query
     * @param string $field     Поле (order_by).
     * @param string $direction Направление (order_to).
     *
     * @return mixed
     */
    public function scopeSorting(
        $query,
        string $field,
        string $direction
    ): mixed {
        if ($field === 'rating') {
            $query->withRating();
        }

        return $query->orderBy($field, $direction);
    }
}
