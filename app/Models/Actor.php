<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Actor extends Model
{
    /** @use HasFactory<\Database\Factories\ActorFactory> */
    use HasFactory;

    protected $hidden =
        [
            'created_at',
            'updated_at',
        ];

    protected $fillable =
        [
            'name',
        ];

    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class)->withTimestamps();
    }
}
