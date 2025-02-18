<?php

namespace App\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use App\Exceptions\InvalidRoleException;

class IsAdminRole {

  public function handle($request, Closure $next) {
    $user = Auth::user();

    if ($user->is_admin) {
      return $next($request);
    }

    throw new InvalidRoleException();
  }
}
