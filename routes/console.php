<?php
// routes/console.php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Register your custom commands here
Artisan::command('cleanup:orphaned-records', function () {
    $this->call(\App\Console\Commands\CleanupOrphanedRecords::class);
})->describe('Delete records that reference non-existent users');