<?php

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

    $api_force = strcasecmp(env('APP_FORCE_API'), 'true') == 0;

    Route::middleware($api_force ? 'api' : 'auth:sanctum')
      ->group(function () {
        Route::get('img/{params}', 'ImageController@index')
          ->where('params', '.*');

        Route::get('mal/{params?}', 'MalController@index');

        Route::get('changelog/{params?}', 'ReleaseController@getLogs');
        Route::get('changelog-be/{params?}', 'ReleaseController@getLogsBE');
        Route::get('issues/{params?}', 'ReleaseController@getIssues');

        Route::get('logs', 'LogController@index');
        Route::get('qualities', 'QualityController@index');
        Route::get('codecs', 'CodecController@index');
        Route::post('import', 'ImportController@index');

        Route::prefix('entries')
          ->group(function () {
            Route::get('', 'EntryController@index');
            Route::get('{uuid}', 'EntryController@get');
            Route::post('', 'EntryController@add');
            Route::put('{uuid}', 'EntryController@edit');
            Route::delete('{uuid}', 'EntryController@delete');
            Route::post('import', 'EntryController@import');

            Route::get('last', 'EntryLastController@index');

            Route::get('by-name', 'EntryByNameController@index');
            Route::get('by-name/{letter}', 'EntryByNameController@get');

            Route::get('by-year', 'EntryByYearController@index');
            Route::get('by-year/{year}', 'EntryByYearController@get');

            Route::get('by-bucket', 'EntryByBucketController@index');
            Route::get('by-bucket/{id}', 'EntryByBucketController@get');

            Route::get('by-sequence/{id}', 'EntryBySequenceController@index');
          });

        Route::prefix('catalogs')
          ->group(function () {
            Route::get('', 'CatalogController@index');
            Route::post('', 'CatalogController@add');
            Route::put('{uuid}', 'CatalogController@edit');
            Route::delete('{uuid}', 'CatalogController@delete');
          });

        Route::prefix('partials')
          ->group(function () {
            Route::get('{uuid}', 'PartialController@index');
            Route::post('', 'PartialController@add');
            Route::put('{uuid}', 'PartialController@edit');
            Route::delete('{uuid}', 'PartialController@delete');

            Route::post('multi/{uuid}', 'PartialController@add_multiple');
            Route::put('multi', 'PartialController@edit_multiple');
          });

        Route::prefix('buckets')
          ->group(function () {
            Route::get('', 'BucketController@index');
            Route::post('', 'BucketController@add');
            Route::put('{id?}', 'BucketController@edit');
            Route::delete('{id}', 'BucketController@delete');
            Route::post('import', 'BucketController@import');
          });

        Route::prefix('sequences')
          ->group(function () {
            Route::get('', 'SequenceController@index');
            Route::post('', 'SequenceController@add');
            Route::put('{id?}', 'SequenceController@edit');
            Route::delete('{id}', 'SequenceController@delete');
            Route::post('import', 'SequenceController@import');
          });

        Route::prefix('groups')
          ->group(function () {
            Route::get('', 'GroupController@index');
            Route::post('', 'GroupController@add');
            Route::delete('{id}', 'GroupController@delete');
          });
      });
  });
