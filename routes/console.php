<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function (ClosureCommand $command) {
    $command->comment(Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote');
