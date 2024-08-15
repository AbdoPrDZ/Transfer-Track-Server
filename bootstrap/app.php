<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    health: '/up',
    )
    ->withBroadcasting(
      __DIR__.'/../routes/channels.php',
      [
        'middleware' => ['auth:sanctum', 'ability:echo'],
      ],
    )
  ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
      'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
      'ability' => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
      'only.localhost' => \App\Http\Middleware\OnlyLocalhostMiddleware::class,
      'auth' => \App\Http\Middleware\Authenticate::class,
      'valid_id' => \App\Http\Middleware\ValidateId::class,
      'item_license' => \App\Http\Middleware\ValidateItemLicense::class,
      'user_license' => \App\Http\Middleware\ValidateUserLicense::class,
    ]);
  })
  ->withExceptions(function (Exceptions $exceptions) {
    //
  })->create();
