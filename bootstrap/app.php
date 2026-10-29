
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureShopIsApproved;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // Preserve Laravel's default web middleware.
        // Append only custom middleware that belongs on every web request.
        $middleware->web(append: [
            SetLocale::class,
            SecurityHeaders::class,
        ]);

        // Register middleware aliases for selected routes.
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'shop.roles' => RoleMiddleware::class,
            'shop.approved' => EnsureShopIsApproved::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
