<?php

namespace App\Middleware;

use Closure;

class ApiManifestGuard {

  public function handle($request, Closure $next) {
    $referer = $request->headers->get('referer');
    $docsUrl = url('/api-docs');

    if (!$referer || !str_contains($referer, $docsUrl)) {
      return response()->json([
        'status' => 403,
        'message' => 'Direct access to JSON is forbidden'
      ], 403);
    }

    return $next($request);
  }
}
