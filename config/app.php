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
  'previous_keys' => [
    ...array_filter(
      explode(',', env('APP_PREVIOUS_KEYS', ''))
    ),
  ],

  /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

  'maintenance' => [
    'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
    'store' => env('APP_MAINTENANCE_STORE', 'database'),
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
  'cloudinary_url' => env('CLOUDINARY_URL', ''),
];
