<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler {
  /**
   * A list of the exception types that are not reported.
   *
   * @var array<int, class-string<Throwable>>
   */
  protected $dontReport = [
    //
  ];

  /**
   * A list of the inputs that are never flashed for validation exceptions.
   *
   * @var array<int, string>
   */
  protected $dontFlash = [
    'current_password',
    'password',
    'password_confirmation',
  ];

  /**
   * Register the exception handling callbacks for the application.
   *
   * @return void
   */
  public function register() {
    $this->reportable(function (Throwable $e) {
      //
    });
  }

  public function render($request, Throwable $e) {
    if ($e instanceof MethodNotAllowedHttpException) {
      return response()->json([
        'status' => 400,
        'message' => 'Invalid request',
      ], 400);
    }

    if ($e instanceof NotFoundHttpException) {
      return response()->json([
        'status' => 404,
        'message' => 'This API endpoint does not exist',
      ], 404);
    }

    return parent::render($request, $e);
  }
}
