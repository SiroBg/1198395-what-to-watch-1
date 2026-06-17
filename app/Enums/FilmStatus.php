<?php

namespace App\Enums;

enum FilmStatus: string
{
    case PENDING = 'pending';
    case MODERATION = 'moderation';
    case READY = 'ready';
}
