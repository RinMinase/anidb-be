<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'index');

Route::prefix('api')
  ->group(function () {
    Route::get(
        '/mal/{params?}',
        'App\Controllers\MalController@index'
      )
      ->name('mal');
  });

Route::middleware('auth:sanctum')
  ->get('/user', function (Request $request) {
    return $request->user();
  });
