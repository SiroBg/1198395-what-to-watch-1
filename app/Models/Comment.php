<?php

namespace App\Models;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @psalm-api
 *
 * @property int $id
 * @property string $text
 * @property string $author_name
 * @property-read User|null $user
 */
class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;

    protected $casts
        = [
            'rating' => 'int',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];

    protected $fillable
        = [
            'text',
            'rating',
            'user_id',
            'film_id',
            'comment_id',
            'created_at',
            'updated_at',
        ];

    /**
     * Возвращает автора отзыва.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Возвращает фильм, к которому относится отзыв.
     */
    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

    /**
     * Возвращает отзыв, к которому относится комментарий.
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }

    /**
     * Возвращает ответы на отзыв.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'comment_id');
    }

    /**
     * Возвращает имя автора комментария.
     */
    public function authorName(): Attribute
    {
        return Attribute::get(
            fn () => $this->user ? $this->user->name : 'Гость',
        );
    }
}
