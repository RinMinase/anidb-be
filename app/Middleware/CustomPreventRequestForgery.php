<?php

namespace App\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

class CustomPreventRequestForgery extends PreventRequestForgery {
  /**
   * The URIs that should be excluded from CSRF verification.
   *
   * @var array<int, string>
   */
  protected $except = [
    'api/*',
  ];
}
