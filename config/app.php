<?php

return [
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
  'api_key' => env('API_KEY'),

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

  /*
   |--------------------------------------------------------------------------
   | Additional Configuration Keys
   |--------------------------------------------------------------------------
   */
  'platform' => env('APP_PLATFORM', 'production'),
  'logs_to_keep' => env('LOGS_TO_KEEP', 200),
  'backups_to_keep' => env('BACKUPS_TO_KEEP', 30),
  'backups_max_days' => env('BACKUPS_MAX_DAYS', 60),
  'anilist_base_uri' => env('ANILIST_BASE_URI', 'graphql.anilist.co'),
  'vehicle_start_date' => env('VEHICLE_START_DATE', '2023-01-01'),
  'registration_root_password' => env('APP_REGISTRATION_ROOT_PASSWORD', ''),
  'cloudinary_url' => env('CLOUDINARY_URL', ''),
];
