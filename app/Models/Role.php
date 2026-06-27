<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @psalm-api
 */
class Role extends Model
{
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
     * Возвращает пользователей с данной ролью.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
