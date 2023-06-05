<?php

use Illuminate\Support\Facades\Route;

Route::pattern('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

Route::middleware('web')
  ->group(function () {

    $isProd = config('app.platform') != 'local';

    Route::view('/', 'index', ['isProd' => $isProd])->name('home');

    if (!$isProd) {
      Route::get('/docs', function () {
        $apidocJsonFile = URL::to('/') . '/docs/api-docs.json';
        $useAbsolutePath = config('l5-swagger.documentations.default.paths.use_absolute_path', true);

        return view('docs', [
          'documentation' => 'default',
          'urlToDocs' => $apidocJsonFile,
          'useAbsolutePath' => $useAbsolutePath,
        ]);
      });
    }
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
        Route::get('mal/{params?}', 'MalController@index');

        // ===== Deprecated =====
        Route::get('changelog/{params?}', 'ReleaseController@getLogs');
        Route::get('changelog-be/{params?}', 'ReleaseController@getLogsBE');
        Route::get('issues/{params?}', 'ReleaseController@getIssues');
        // ======================

        Route::get('management', 'ManagementController@index');
        Route::get('logs', 'LogController@index');
        Route::get('qualities', 'QualityController@index');
        Route::get('priorities', 'PriorityController@index');
        Route::post('import', 'ImportController@index');

        Route::prefix('entries')
          ->group(function () {
            Route::get('', 'EntryController@index');
            Route::get('{uuid}', 'EntryController@get');
            Route::post('', 'EntryController@add');
            Route::put('{uuid}', 'EntryController@edit');
            Route::delete('{uuid}', 'EntryController@delete');
            Route::post('import', 'EntryController@import');

            Route::put('img-upload/{uuid}', 'EntryController@imageUpload');
            Route::put('ratings/{uuid}', 'EntryController@ratings');
            Route::get('titles', 'EntryController@getTitles');

            Route::post('rewatch/{uuid}', 'EntryController@rewatchAdd');
            Route::delete('rewatch/{uuid}', 'EntryController@rewatchDelete');

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
            Route::get('{uuid}', 'CatalogController@get');
            Route::post('', 'CatalogController@add');
            Route::put('{uuid}', 'CatalogController@edit');
            Route::delete('{uuid}', 'CatalogController@delete');
          });

        Route::prefix('partials')
          ->group(function () {

            // ======= Unused =======
            // Route::get('{uuid}', 'PartialController@index');
            // ======================

            Route::post('', 'PartialController@add');
            Route::put('{uuid}', 'PartialController@edit');
            Route::delete('{uuid}', 'PartialController@delete');

            Route::post('multi', 'PartialController@add_multiple');
            Route::put('multi/{uuid}', 'PartialController@edit_multiple');
          });

        Route::prefix('buckets')
          ->group(function () {

            // ======= Unused =======
            // Route::get('', 'BucketController@index');
            // Route::post('', 'BucketController@add');
            // Route::put('{id?}', 'BucketController@edit');
            // Route::delete('{id}', 'BucketController@delete');
            // ======================

            Route::post('import', 'BucketController@import');
          });

        Route::prefix('bucket-sims')
          ->group(function () {
            Route::get('', 'BucketSimController@index');
            Route::get('{id}', 'BucketSimController@get');
            Route::post('', 'BucketSimController@add');
            Route::put('{id}', 'BucketSimController@edit');
            Route::delete('{id}', 'BucketSimController@delete');

            Route::post('{id}', 'BucketSimController@saveBucket');
          });

        Route::prefix('sequences')
          ->group(function () {
            Route::get('', 'SequenceController@index');
            Route::get('{id}', 'SequenceController@get');
            Route::post('', 'SequenceController@add');
            Route::put('{id?}', 'SequenceController@edit');
            Route::delete('{id}', 'SequenceController@delete');
            Route::post('import', 'SequenceController@import');
          });

        Route::prefix('groups')
          ->group(function () {
            Route::get('', 'GroupController@index');
            Route::get('names', 'GroupController@getNames');
            Route::post('', 'GroupController@add');
            Route::put('{uuid}', 'GroupController@edit');
            Route::delete('{uuid}', 'GroupController@delete');
            Route::post('import', 'GroupController@import');
          });

        Route::prefix('codecs')
          ->group(function () {
            Route::get('', 'CodecController@index');

            Route::prefix('audio')
              ->group(function () {
                Route::get('', 'CodecController@getAudio');
                Route::post('', 'CodecController@addAudio');
                Route::put('{id}', 'CodecController@editAudio');
                Route::delete('{id}', 'CodecController@deleteAudio');
              });

            Route::prefix('video')
              ->group(function () {
                Route::get('', 'CodecController@getVideo');
                Route::post('', 'CodecController@addVideo');
                Route::put('{id}', 'CodecController@editVideo');
                Route::delete('{id}', 'CodecController@deleteVideo');
              });
          });

        Route::prefix('rss')
          ->group(function () {
            Route::get('', 'RssController@index');
            Route::get('{uuid}', 'RssController@get');
            Route::post('', 'RssController@add');
            Route::put('{uuid}', 'RssController@edit');
            Route::delete('{uuid}', 'RssController@delete');

            Route::post('read/{uuid}', 'RssController@read');
            Route::delete('read/{uuid}', 'RssController@unread');

            Route::post('bookmark/{uuid}', 'RssController@bookmark');
            Route::delete('bookmark/{uuid}', 'RssController@removeBookmark');
          });
      });
  });
