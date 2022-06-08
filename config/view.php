<?php

return [

  /* View / Blade Storage Paths */

  'paths' => [
    realpath(base_path('app')),
  ],

  /* Compiled View Path */

  'compiled' => env(
    'VIEW_COMPILED_PATH',
    realpath(storage_path('framework/views'))
  ),

];
