<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::pattern('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

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

    $isDevelopment = strcasecmp(env('APP_ENV'), 'local') == 0;

    Route::middleware($isDevelopment ? 'api' : 'auth:sanctum')
      ->group(function () {
        Route::get('img/{params}', 'ImageController@index')
          ->where('params', '.*');

        Route::get('mal/{params?}', 'MalController@index');

        Route::get('changelog/{params?}', 'ReleaseController@getLogs');
        Route::get('changelog-be/{params?}', 'ReleaseController@getLogsBE');
        Route::get('issues/{params?}', 'ReleaseController@getIssues');

        Route::get('sequences', 'SequenceController@index');

        Route::get('logs', 'LogController@index');

        Route::get('qualities', 'QualityController@index');

        Route::prefix('entries')
          ->group(function () {
            Route::get('', 'EntryController@index');
            Route::get('{uuid}', 'EntryController@get');
            Route::post('', 'EntryController@add');
            Route::put('{uuid}', 'EntryController@edit');
            Route::delete('{uuid}', 'EntryController@delete');

            Route::get('last', 'EntryController@getLast');

            Route::get('by-name', 'EntryController@getByName');
            Route::get('by-name/{letter}', 'EntryController@getByLetter');

            Route::get('by-year', 'EntryController@getByYear');
            Route::get('by-year/{year}', 'EntryController@getBySeason');

            Route::get('by-bucket', 'EntryController@getBuckets');
            Route::get('by-bucket/{id}', 'EntryController@getByBucket');
          });

        Route::prefix('catalogs')
          ->group(function () {
            Route::get('', 'CatalogController@index');
            Route::get('{id}', 'CatalogController@get');
            Route::post('', 'CatalogController@add');
            Route::put('{id?}', 'CatalogController@edit');
            Route::delete('{id}', 'CatalogController@delete');
          });

        Route::prefix('partials')
          ->group(function () {
            Route::post('', 'PartialController@add');
            Route::put('{id?}', 'PartialController@edit');
            Route::delete('{id}', 'PartialController@delete');
          });

        Route::get('buckets')
          ->group(function () {
            Route::get('', 'BucketController@index');
            Route::get('{id}', 'BucketController@get');
            Route::post('', 'BucketController@add');
            Route::put('{id?}', 'BucketController@edit');
            Route::delete('{id}', 'BucketController@delete');
          });
      });
  });
