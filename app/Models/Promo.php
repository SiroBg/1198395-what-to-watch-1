<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @psalm-api
 */
class Promo extends Model
{
    protected $hidden
        = [
            'created_at',
            'updated_at',
        ];

    protected $fillable
        = [
            'film_id',
        ];

    /**
     * Возвращает промо фильм.
     *
     * @return BelongsTo
     */
    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }
}
