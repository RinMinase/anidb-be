<?php

use Illuminate\Support\Str;

// https://github.com/laravel/laravel/blob/11.x/config/session.php

return [

  'driver' => env('SESSION_DRIVER', 'file'),

  // number of minutes for session expiry
  'lifetime' => 1440,
];
