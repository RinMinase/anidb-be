<?php

return [

  // Application-related config
  'name' => env('APP_NAME', 'Rin\'s AniDB API'),
  'timezone' => 'UTC',
  'locale' => 'en',
  'fallback_locale' => 'en',
  'faker_locale' => 'en_US',

  /*
   |--------------------------------------------------------------------------
   | Application Environment
   |--------------------------------------------------------------------------
   |
   | This value determines the "environment" your application is currently
   | running in. This may determine how you prefer to configure various
   | services the application utilizes. Set this in your ".env" file.
   |
   */

  'env' => env('APP_ENV', 'production'),

  /*
   |--------------------------------------------------------------------------
   | Application Debug Mode
   |--------------------------------------------------------------------------
   |
   | When your application is in debug mode, detailed error messages with
   | stack traces will be shown on every error that occurs within your
   | application. If disabled, a simple generic error page is shown.
   |
   */

  'debug' => (bool) env('APP_DEBUG', false),

  /*
   |--------------------------------------------------------------------------
   | Application URL
   |--------------------------------------------------------------------------
   |
   | This URL is used by the console to properly generate URLs when using
   | the Artisan command line tool. You should set this to the root of
   | your application so that it is used when running Artisan tasks.
   |
   */

  'url' => env('APP_URL', null),
  'asset_url' => env('ASSET_URL', null),

  /*
   |--------------------------------------------------------------------------
   | Encryption Key
   |--------------------------------------------------------------------------
   |
   | This key is used by the Illuminate encrypter service and should be set
   | to a random, 32 character string, otherwise these encrypted strings
   | will not be safe. Please do this before deploying an application!
   |
   */

  'key' => env('APP_KEY'),
  'cipher' => 'AES-256-CBC',

  /*
   |--------------------------------------------------------------------------
   | Autoloaded Service Providers
   |--------------------------------------------------------------------------
   |
   | The service providers listed here will be automatically loaded on the
   | request to your application. Feel free to add your own services to
   | this array to grant expanded functionality to your applications.
   |
   */

  'providers' => [

    /**
     * Laravel Framework Service Providers...
     */
    Illuminate\Auth\AuthServiceProvider::class,
    Illuminate\Broadcasting\BroadcastServiceProvider::class,
    Illuminate\Bus\BusServiceProvider::class,
    Illuminate\Cache\CacheServiceProvider::class,
    Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
    Illuminate\Cookie\CookieServiceProvider::class,
    Illuminate\Database\DatabaseServiceProvider::class,
    Illuminate\Encryption\EncryptionServiceProvider::class,
    Illuminate\Filesystem\FilesystemServiceProvider::class,
    Illuminate\Foundation\Providers\FoundationServiceProvider::class,
    Illuminate\Hashing\HashServiceProvider::class,
    Illuminate\Mail\MailServiceProvider::class,
    Illuminate\Notifications\NotificationServiceProvider::class,
    Illuminate\Pagination\PaginationServiceProvider::class,
    Illuminate\Pipeline\PipelineServiceProvider::class,
    Illuminate\Queue\QueueServiceProvider::class,
    Illuminate\Redis\RedisServiceProvider::class,
    Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
    Illuminate\Session\SessionServiceProvider::class,
    Illuminate\Translation\TranslationServiceProvider::class,
    Illuminate\Validation\ValidationServiceProvider::class,
    Illuminate\View\ViewServiceProvider::class,

    /**
     * Application Service Providers...
     */
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    // App\Providers\BroadcastServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\RouteServiceProvider::class,

    /**
     * Third party providers
     */
    CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider::class,
  ],

  /*
   |--------------------------------------------------------------------------
   | Class Aliases
   |--------------------------------------------------------------------------
   |
   | This array of class aliases will be registered when this application
   | is started. However, feel free to register as many as you wish as
   | the aliases are "lazy" loaded so they don't hinder performance.
   |
   */

  'aliases' => [

    'App' => Illuminate\Support\Facades\App::class,
    'Arr' => Illuminate\Support\Arr::class,
    'Artisan' => Illuminate\Support\Facades\Artisan::class,
    'Auth' => Illuminate\Support\Facades\Auth::class,
    'Blade' => Illuminate\Support\Facades\Blade::class,
    'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
    'Bus' => Illuminate\Support\Facades\Bus::class,
    'Cache' => Illuminate\Support\Facades\Cache::class,
    'Config' => Illuminate\Support\Facades\Config::class,
    'Cookie' => Illuminate\Support\Facades\Cookie::class,
    'Crypt' => Illuminate\Support\Facades\Crypt::class,
    'Date' => Illuminate\Support\Facades\Date::class,
    'DB' => Illuminate\Support\Facades\DB::class,
    'Eloquent' => Illuminate\Database\Eloquent\Model::class,
    'Event' => Illuminate\Support\Facades\Event::class,
    'File' => Illuminate\Support\Facades\File::class,
    'Gate' => Illuminate\Support\Facades\Gate::class,
    'Hash' => Illuminate\Support\Facades\Hash::class,
    'Http' => Illuminate\Support\Facades\Http::class,
    'Js' => Illuminate\Support\Js::class,
    'Lang' => Illuminate\Support\Facades\Lang::class,
    'Log' => Illuminate\Support\Facades\Log::class,
    'Mail' => Illuminate\Support\Facades\Mail::class,
    'Notification' => Illuminate\Support\Facades\Notification::class,
    'Password' => Illuminate\Support\Facades\Password::class,
    'Queue' => Illuminate\Support\Facades\Queue::class,
    'RateLimiter' => Illuminate\Support\Facades\RateLimiter::class,
    'Redirect' => Illuminate\Support\Facades\Redirect::class,
    // 'Redis' => Illuminate\Support\Facades\Redis::class,
    'Request' => Illuminate\Support\Facades\Request::class,
    'Response' => Illuminate\Support\Facades\Response::class,
    'Route' => Illuminate\Support\Facades\Route::class,
    'Schema' => Illuminate\Support\Facades\Schema::class,
    'Session' => Illuminate\Support\Facades\Session::class,
    'Storage' => Illuminate\Support\Facades\Storage::class,
    'Str' => Illuminate\Support\Str::class,
    'URL' => Illuminate\Support\Facades\URL::class,
    'Validator' => Illuminate\Support\Facades\Validator::class,
    'View' => Illuminate\Support\Facades\View::class,

    'Cloudinary' => CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::class,

  ],

  /*
   |--------------------------------------------------------------------------
   | Additional Configuration Keys
   |--------------------------------------------------------------------------
   */
  'platform' => env('APP_PLATFORM', 'production'),
  'logs_to_keep' => env('LOGS_TO_KEEP', 200),
  'anilist_base_uri' => env('ANILIST_BASE_URI', 'graphql.anilist.co'),
  'vehicle_start_date' => env('VEHICLE_START_DATE', '2023-01-01'),
  'registration_root_password' => env('APP_REGISTRATION_ROOT_PASSWORD', ''),
];
