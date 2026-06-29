<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function (ClosureCommand $command) {
    $command->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
