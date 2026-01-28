<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register your custom middleware aliases HERE
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'check.approval' => \App\Http\Middleware\CheckApprovalStatus::class,
            'log.attendance' => \App\Http\Middleware\LogUserAttendance::class,
        ]);
        
        // Apply attendance logging only to authenticated routes
        $middleware->web(append: [
            \App\Http\Middleware\CheckApprovalStatus::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();