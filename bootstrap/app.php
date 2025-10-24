<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
   ->withMiddleware(function (Middleware $middleware) {

    // ¡ESTE ES EL CÓDIGO CORRECTO!
    // Ahora sí funcionará, porque el archivo ya existe.
    $middleware->alias([
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    ]);

    // ------ Tu código existente (no lo toques) ------
    $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

    $middleware->web(append: [
        HandleAppearance::class,
        HandleInertiaRequests::class,
        AddLinkHeadersForPreloadedAssets::class,
    ]);
    // ------ Fin de tu código existente ------
})
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
