<?php

namespace App\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register() {
    //
  }

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot() {
    Storage::disk('local')->buildTemporaryUrlsUsing(function ($path, $expiration, $options) {
      return URL::temporarySignedRoute(
        'files.download', // route name
        $expiration,
        array_merge($options, ['path' => $path])
      );
    });
  }
}
