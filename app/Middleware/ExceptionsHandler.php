<?php

namespace App\Middleware;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionsHandler extends ExceptionHandler
{
	protected $dontReport = [
		AuthorizationException::class,
		HttpException::class,
		ModelNotFoundException::class,
		ValidationException::class,
	];

	public function report(Exception $exception)
	{
		parent::report($exception);
	}

	public function render($request, Exception $exception)
	{
		return parent::render($request, $exception);
	}
}
