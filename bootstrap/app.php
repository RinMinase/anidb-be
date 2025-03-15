<?php

/* Create The Application */

use Illuminate\Auth\AuthenticationException;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Console\PruneOldLogData;
use App\Exceptions\CustomException;


return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    web: __DIR__ . '/routes.php',
    health: '/status',
  )

  ->withCommands([__DIR__ . '/../app/Commands'])

  ->withMiddleware(function (Middleware $middleware) {
    $middleware->web(replace: [
      Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class => App\Middleware\VerifyCsrfToken::class,
    ]);

    $middleware->api(prepend: [
      'throttle:api',
    ]);
  })

  ->withExceptions(function (Exceptions $exceptions) {
    $exceptions->render(function (Exception $e) {
      if ($e instanceof MethodNotAllowedHttpException)
        return response()->json(['status' => 400, 'message' => 'Invalid requestssss'], 400);

      if ($e instanceof NotFoundHttpException)
        return response()->json(['status' => 404, 'message' => 'This API endpoint does not exist'], 404);

      if ($e instanceof AuthenticationException)
        return response()->json(['status' => 401, 'message' => 'Unauthorized'], 401);

      if ($e instanceof ModelNotFoundException)
        return response()->json(['status' => 404, 'message' => 'The provided ID is invalid, or the item does not exist'], 404);

      $is_prod = config('app.platform') != 'local';
      $has_no_custom_exception = !$e instanceof CustomException;

      if ($has_no_custom_exception && $e instanceof Exception && $is_prod) {
        return response()->json([
          'status' => 500,
          'message' => 'Failed',
        ], 500);
      }
    });
  })

  ->withSchedule(function (Schedule $schedule) {
    $schedule->command(PruneOldLogData::class)->everyFiveSeconds();
  })

  ->create();
