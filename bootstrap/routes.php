<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('web')
  ->group(function () {
    Route::view('/', 'index')->name('home');
  });

Route::prefix('api')
  ->middleware('api')
  ->namespace('App\Controllers')
  ->group(function () {

    Route::prefix('auth')
      ->group(function () {
        Route::post('register', 'AuthController@register');
        Route::post('login', 'AuthController@login')->name('login');

        Route::middleware('auth:sanctum')
          ->group(function () {
            Route::get('user', 'AuthController@getUser');
            Route::post('logout', 'AuthController@logout');
          });
      });


    Route::middleware('auth:sanctum')
      ->group(function () {
        Route::get('img/{params}', 'ImageController@index')
          ->where('params', '.*');

        Route::get('mal/{params?}', 'MalController@index');

        Route::get('changelog/{params?}', 'ReleaseController@getLogs');
        Route::get('changelog-be/{params?}', 'ReleaseController@getLogsBE');
        Route::get('issues/{params?}', 'ReleaseController@getIssues');

        Route::get('hdd', 'HddController@index');

        Route::get('marathon', 'MarathonController@index');

        Route::get('log', 'LogController@index');

        Route::prefix('entry')
          ->group(function () {
            Route::get('', 'EntryController@index');
            Route::get('{id}', 'EntryController@get');
            Route::post('', 'EntryController@add');
            Route::put('{id}', 'EntryController@edit');
            Route::delete('{id}', 'EntryController@delete');
          });
      });
  });
