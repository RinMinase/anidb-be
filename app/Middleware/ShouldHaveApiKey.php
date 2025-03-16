<?php

namespace App\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;

class ShouldHaveApiKey {

  public function handle($request, Closure $next) {
    $key = $request->header('x-api-key');

    if (!$key || $key !== config('app.api_key')) {
      throw new AuthenticationException('Wrong api key');
    }

    return $next($request);
  }
}
