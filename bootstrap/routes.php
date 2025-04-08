<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Middleware\IsAdminRole;
use App\Middleware\ShouldHaveApiKey;

Route::pattern('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
Route::pattern('uuid2', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
Route::pattern('integer', '[0-9]+');
Route::pattern('string', '[a-zA-z%0-9 ]+');
Route::pattern('year', '^19\d{2}|2\d{3}$');

Route::middleware('web')
  ->group(function () {

    $isProd = config('app.platform') != 'local';

    Route::view('/', 'index', ['isProd' => $isProd])->name('home');

    Route::get('/health', function () {
      $exception = null;

      try {
        // Check database layer
        // Check API layer
        // Check Scraper layer
        // Check cloudinary layer

        // throw new Exception('Trigger exception');
      } catch (Exception $e) {
        $exception = $e->getMessage();
      }

      return view('health', ['exception' => $exception]);
    });

    if (!$isProd) {
      Route::get('/api-docs', function () {
        $apidocJsonFile = URL::to('/') . '/docs';
        $useAbsolutePath = config('l5-swagger.documentations.default.paths.use_absolute_path');

        return view('docs', [
          'documentation' => 'default',
          'urlToDocs' => $apidocJsonFile,
          'useAbsolutePath' => $useAbsolutePath,
        ]);
      });
    } else {
      Route::get('/api-docs', function () {
        throw new NotFoundHttpException;
      });
    }
  });

Route::prefix('api')
  ->middleware('api')
  ->namespace('App\Fourleaf\Controllers')
  ->group(function () {

    Route::prefix('fourleaf')
      ->group(function () {

        Route::prefix('gas')
          ->group(function () {
            Route::get('', [\App\Fourleaf\Controllers\GasController::class, 'get']);

            Route::get('odo', [\App\Fourleaf\Controllers\GasController::class, 'getOdo']);

            Route::get('fuel', [\App\Fourleaf\Controllers\GasController::class, 'getFuel']);
            Route::post('fuel', [\App\Fourleaf\Controllers\GasController::class, 'addFuel']);
            Route::put('fuel/{id}', [\App\Fourleaf\Controllers\GasController::class, 'editFuel']);
            Route::delete('fuel/{id}', [\App\Fourleaf\Controllers\GasController::class, 'deleteFuel']);

            Route::get('maintenance', [\App\Fourleaf\Controllers\GasController::class, 'getMaintenance']);
            Route::get('maintenance/parts', [\App\Fourleaf\Controllers\GasController::class, 'getMaintenanceParts']);
            Route::post('maintenance', [\App\Fourleaf\Controllers\GasController::class, 'addMaintenance']);
            Route::put('maintenance/{id}', [\App\Fourleaf\Controllers\GasController::class, 'editMaintenance']);
            Route::delete('maintenance/{id}', [\App\Fourleaf\Controllers\GasController::class, 'deleteMaintenance']);
          });

        Route::prefix('electricity')
          ->group(function () {
            Route::get('', [\App\Fourleaf\Controllers\ElectricityController::class, 'get']);
            Route::post('', [\App\Fourleaf\Controllers\ElectricityController::class, 'add']);
            Route::put('{id}', [\App\Fourleaf\Controllers\ElectricityController::class, 'edit']);
            Route::delete('{id}', [\App\Fourleaf\Controllers\ElectricityController::class, 'delete']);
          });

        Route::prefix('bills')
          ->group(function () {
            Route::prefix('electricity')
              ->group(function () {
                Route::get('', [\App\Fourleaf\Controllers\BillsController::class, 'get']);
                Route::post('', [\App\Fourleaf\Controllers\BillsController::class, 'add']);
                Route::put('{uuid}', [\App\Fourleaf\Controllers\BillsController::class, 'edit']);
                Route::delete('{uuid}', [\App\Fourleaf\Controllers\BillsController::class, 'delete']);
              });
          });
      });
  });

Route::prefix('api')
  ->middleware('api')
  ->namespace('App\Controllers')
  ->group(function () {

    Route::get('local/temp/{path}', [\App\Controllers\ExportController::class, 'download'])
      ->where('path', '(.*)')
      ->middleware('signed')
      ->withoutMiddleware(ShouldHaveApiKey::class)
      ->name('files.download');

    Route::prefix('auth')
      ->group(function () {
        Route::post('register', [\App\Controllers\AuthController::class, 'register']);
        Route::post('login', [\App\Controllers\AuthController::class, 'login'])->name('login');

        Route::middleware('auth:sanctum')
          ->group(function () {
            Route::get('user', [\App\Controllers\AuthController::class, 'getUser']);
            Route::post('logout', [\App\Controllers\AuthController::class, 'logout']);
          });
      });

    Route::middleware('auth:sanctum')
      ->group(function () {

        // Importing from old system
        Route::prefix('archaic')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::post('import', [\App\Controllers\ImportController::class, 'import_archaic_format']);
            Route::post('import/entries', [\App\Controllers\EntryController::class, 'import']);
            Route::post('import/buckets', [\App\Controllers\BucketController::class, 'import']);
            Route::post('import/sequences', [\App\Controllers\SequenceController::class, 'import']);
            Route::post('import/groups', [\App\Controllers\GroupController::class, 'import']);
          });

        Route::get('import', [\App\Controllers\ImportController::class, 'import_new_format'])->middleware(IsAdminRole::class);

        Route::prefix('exports')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::get('', [\App\Controllers\ExportController::class, 'index']);
            Route::get('{uuid}', [\App\Controllers\ExportController::class, 'generate_download_url']);

            Route::post('sql', [\App\Controllers\ExportController::class, 'generate_sql']);
            Route::post('json', [\App\Controllers\ExportController::class, 'generate_json']);
            Route::post('xlsx', [\App\Controllers\ExportController::class, 'generate_xlsx']);
          });

        // Dropdowns
        Route::get('genres', [\App\Controllers\GenreController::class, 'index'])->middleware(IsAdminRole::class);
        Route::get('qualities', [\App\Controllers\QualityController::class, 'index'])->middleware(IsAdminRole::class);
        Route::get('priorities', [\App\Controllers\PriorityController::class, 'index'])->middleware(IsAdminRole::class);

        // All-in-one for Adding Entries
        // Groups + Qualities + Codecs + Genres + Watchers
        Route::get('dropdowns', [\App\Controllers\DropdownController::class, 'index'])->middleware(IsAdminRole::class);

        Route::get('logs', [\App\Controllers\LogController::class, 'index'])->middleware(IsAdminRole::class);

        Route::prefix('management')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::get('', [\App\Controllers\ManagementController::class, 'index']);
            Route::get('by-year', [\App\Controllers\ManagementController::class, 'get_by_year']);
          });

        Route::prefix('users')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::get('', [\App\Controllers\UserController::class, 'index']);
            Route::get('{uuid}', [\App\Controllers\UserController::class, 'get']);
            Route::post('', [\App\Controllers\UserController::class, 'add']);
            Route::put('{uuid}', [\App\Controllers\UserController::class, 'edit']);
            Route::delete('{uuid}', [\App\Controllers\UserController::class, 'delete']);
          });

        // Non Admin Entry Routes
        Route::prefix('entries')
          ->group(function () {
            Route::get('', [\App\Controllers\EntryController::class, 'index']);
            Route::get('{uuid}', [\App\Controllers\EntryController::class, 'get']);
            Route::get('titles', [\App\Controllers\EntryController::class, 'get_titles']);
          });

        // Admin-required Entry Routes
        Route::prefix('entries')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::post('', [\App\Controllers\EntryController::class, 'add']);
            Route::put('{uuid}', [\App\Controllers\EntryController::class, 'edit']);
            Route::delete('{uuid}', [\App\Controllers\EntryController::class, 'delete']);
            Route::get('search', [\App\Controllers\EntryController::class, 'search']);
            Route::get('watchers', [\App\Controllers\EntryController::class, 'get_watchers']);

            Route::post('{uuid}/offquel/{uuid2}', [\App\Controllers\EntryController::class, 'add_offquel']);
            Route::delete('{uuid}/offquel/{uuid2}', [\App\Controllers\EntryController::class, 'delete_offquel']);
            Route::put('img-upload/{uuid}', [\App\Controllers\EntryController::class, 'imageUpload']);
            Route::delete('img-upload/{uuid}', [\App\Controllers\EntryController::class, 'imageDelete']);
            Route::put('ratings/{uuid}', [\App\Controllers\EntryController::class, 'ratings']);

            Route::post('rewatch/{uuid}', [\App\Controllers\EntryController::class, 'rewatchAdd']);
            Route::delete('rewatch/{uuid}', [\App\Controllers\EntryController::class, 'rewatchDelete']);

            Route::get('last', [\App\Controllers\EntryLastController::class, 'index']);

            Route::get('by-name', [\App\Controllers\EntryByNameController::class, 'index']);
            Route::get('by-name/{letter}', [\App\Controllers\EntryByNameController::class, 'get']);

            Route::get('by-year', [\App\Controllers\EntryByYearController::class, 'index']);
            Route::get('by-year/{year}', [\App\Controllers\EntryByYearController::class, 'get']);
            Route::get('by-year/uncategorized', [\App\Controllers\EntryByYearController::class, 'get']);

            Route::get('by-genre', [\App\Controllers\EntryByGenreController::class, 'index']);
            Route::get('by-genre/{string}', [\App\Controllers\EntryByGenreController::class, 'get']);

            Route::get('by-bucket', [\App\Controllers\EntryByBucketController::class, 'index']);
            Route::get('by-bucket/{id}', [\App\Controllers\EntryByBucketController::class, 'get']);

            Route::get('by-sequence/{id}', [\App\Controllers\EntryBySequenceController::class, 'index']);
          });

        Route::prefix('catalogs')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::get('', [\App\Controllers\CatalogController::class, 'index']);
            Route::post('', [\App\Controllers\CatalogController::class, 'add']);
            Route::put('{uuid}', [\App\Controllers\CatalogController::class, 'edit']);
            Route::delete('{uuid}', [\App\Controllers\CatalogController::class, 'delete']);

            Route::get('{uuid}/partials', [\App\Controllers\CatalogController::class, 'get']);
          });

        Route::prefix('partials')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::get('', [\App\Controllers\PartialController::class, 'index']);
            Route::get('{uuid}', [\App\Controllers\PartialController::class, 'get']);
            Route::post('', [\App\Controllers\PartialController::class, 'add']);
            Route::put('{uuid}', [\App\Controllers\PartialController::class, 'edit']);
            Route::delete('{uuid}', [\App\Controllers\PartialController::class, 'delete']);

            Route::post('multi', [\App\Controllers\PartialController::class, 'add_multiple']);
            Route::put('multi/{uuid}', [\App\Controllers\PartialController::class, 'edit_multiple']);
          });

        Route::prefix('bucket-sims')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::get('', [\App\Controllers\BucketSimController::class, 'index']);
            Route::get('{uuid}', [\App\Controllers\BucketSimController::class, 'get']);
            Route::post('', [\App\Controllers\BucketSimController::class, 'add']);
            Route::put('{uuid}', [\App\Controllers\BucketSimController::class, 'edit']);
            Route::delete('{uuid}', [\App\Controllers\BucketSimController::class, 'delete']);

            Route::post('save/{uuid}', [\App\Controllers\BucketSimController::class, 'saveBucket']);
            Route::post('clone/{uuid}', [\App\Controllers\BucketSimController::class, 'clone']);
            Route::post('preview', [\App\Controllers\BucketSimController::class, 'preview']);
            Route::post('backup', [\App\Controllers\BucketSimController::class, 'backup']);
          });

        Route::prefix('sequences')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::get('', [\App\Controllers\SequenceController::class, 'index']);
            Route::get('{id}', [\App\Controllers\SequenceController::class, 'get']);
            Route::post('', [\App\Controllers\SequenceController::class, 'add']);
            Route::put('{id?}', [\App\Controllers\SequenceController::class, 'edit']);
            Route::delete('{id}', [\App\Controllers\SequenceController::class, 'delete']);
          });

        Route::prefix('groups')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::get('', [\App\Controllers\GroupController::class, 'index']);
            Route::get('names', [\App\Controllers\GroupController::class, 'getNames']);
            Route::post('', [\App\Controllers\GroupController::class, 'add']);
            Route::put('{uuid}', [\App\Controllers\GroupController::class, 'edit']);
            Route::delete('{uuid}', [\App\Controllers\GroupController::class, 'delete']);
          });

        Route::prefix('codecs')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::get('', [\App\Controllers\CodecController::class, 'index']);

            Route::prefix('audio')
              ->group(function () {
                Route::get('', [\App\Controllers\CodecController::class, 'getAudio']);
                Route::post('', [\App\Controllers\CodecController::class, 'addAudio']);
                Route::put('{id}', [\App\Controllers\CodecController::class, 'editAudio']);
                Route::delete('{id}', [\App\Controllers\CodecController::class, 'deleteAudio']);
              });

            Route::prefix('video')
              ->group(function () {
                Route::get('', [\App\Controllers\CodecController::class, 'getVideo']);
                Route::post('', [\App\Controllers\CodecController::class, 'addVideo']);
                Route::put('{id}', [\App\Controllers\CodecController::class, 'editVideo']);
                Route::delete('{id}', [\App\Controllers\CodecController::class, 'deleteVideo']);
              });
          });

        Route::prefix('anilist')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::get('title/{integer}', [\App\Controllers\AnilistController::class, 'get']);
            Route::get('search', [\App\Controllers\AnilistController::class, 'search']);
          });

        Route::prefix('pc')
          ->middleware(IsAdminRole::class)
          ->group(function () {

            Route::post('import', [\App\Controllers\PCController::class, 'import']);

            Route::prefix('owners')
              ->group(function () {
                Route::get('', [\App\Controllers\PCOwnerController::class, 'index']);
                Route::get('{uuid}', [\App\Controllers\PCOwnerController::class, 'get']);
                Route::post('', [\App\Controllers\PCOwnerController::class, 'add']);
                Route::put('{uuid}', [\App\Controllers\PCOwnerController::class, 'edit']);
                Route::delete('{uuid}', [\App\Controllers\PCOwnerController::class, 'delete']);
                Route::post('import', [\App\Controllers\PCOwnerController::class, 'import']);
              });

            Route::prefix('infos')
              ->group(function () {
                Route::get('{uuid}', [\App\Controllers\PCInfoController::class, 'get']);
                Route::post('', [\App\Controllers\PCInfoController::class, 'add']);
                Route::put('{uuid}', [\App\Controllers\PCInfoController::class, 'edit']);
                Route::delete('{uuid}', [\App\Controllers\PCInfoController::class, 'delete']);
                Route::post('import', [\App\Controllers\PCInfoController::class, 'import']);

                Route::post('{uuid}/duplicate', [\App\Controllers\PCInfoController::class, 'duplicate']);
                Route::put('{uuid}/hide', [\App\Controllers\PCInfoController::class, 'toggle_hide']);
              });

            Route::prefix('components')
              ->group(function () {
                Route::get('', [\App\Controllers\PCComponentController::class, 'index']);
                Route::get('{id}', [\App\Controllers\PCComponentController::class, 'get']);
                Route::post('', [\App\Controllers\PCComponentController::class, 'add']);
                Route::put('{id}', [\App\Controllers\PCComponentController::class, 'edit']);
                Route::delete('{id}', [\App\Controllers\PCComponentController::class, 'delete']);
                Route::post('import', [\App\Controllers\PCComponentController::class, 'import']);
              });

            Route::prefix('setups')
              ->group(function () {
                Route::post('import', [\App\Controllers\PCSetupController::class, 'import']);
              });

            Route::prefix('types')
              ->group(function () {
                Route::get('', [\App\Controllers\PCComponentTypeController::class, 'index']);
                Route::post('', [\App\Controllers\PCComponentTypeController::class, 'add']);
                Route::put('{id}', [\App\Controllers\PCComponentTypeController::class, 'edit']);
                Route::delete('{id}', [\App\Controllers\PCComponentTypeController::class, 'delete']);
              });
          });
      });
  });
