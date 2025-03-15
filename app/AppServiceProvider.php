<?php

namespace App;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {

  public function register() {
  }

  public function boot() {
    RateLimiter::for('api', function (Request $request) {
      return Limit::perMinute(60)
        ->by(optional($request->user())->id ?: $request->ip());
    });

    Storage::disk('local')->buildTemporaryUrlsUsing(function ($path, $expiration, $options) {
      return URL::temporarySignedRoute(
        'files.download', // route name
        $expiration,
        array_merge($options, ['path' => $path])
      );
    });
  }
}
