<?php

namespace App\Enums;

enum FilmStatus: string
{
    case Pending = 'pending';
    case Moderation = 'moderation';
    case Ready = 'ready';
}
