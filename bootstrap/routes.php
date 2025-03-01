<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Controllers\AnilistController;
use App\Controllers\AuthController;
use App\Controllers\BucketController;
use App\Controllers\BucketSimController;
use App\Controllers\CatalogController;
use App\Controllers\CodecController;
use App\Controllers\DropdownController;
use App\Controllers\EntryByBucketController;
use App\Controllers\EntryByGenreController;
use App\Controllers\EntryByNameController;
use App\Controllers\EntryBySequenceController;
use App\Controllers\EntryByYearController;
use App\Controllers\EntryController;
use App\Controllers\EntryLastController;
use App\Controllers\GenreController;
use App\Controllers\GroupController;
use App\Controllers\ImportController;
use App\Controllers\LogController;
use App\Controllers\ManagementController;
use App\Controllers\PartialController;
use App\Controllers\PCOwnerController;
use App\Controllers\PCComponentController;
use App\Controllers\PCComponentTypeController;
use App\Controllers\PCController;
use App\Controllers\PCInfoController;
use App\Controllers\PCSetupController;
use App\Controllers\PriorityController;
use App\Controllers\QualityController;
use App\Controllers\RssController;
use App\Controllers\SequenceController;
use App\Controllers\UserController;
use App\Fourleaf\Controllers\BillsController;
use App\Fourleaf\Controllers\ElectricityController;
use App\Fourleaf\Controllers\GasController;

use App\Middleware\IsAdminRole;

Route::pattern('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
Route::pattern('uuid2', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
Route::pattern('integer', '[0-9]+');
Route::pattern('string', '[a-zA-z%0-9 ]+');
Route::pattern('year', '^19\d{2}|2\d{3}$');

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
    } else {
      Route::get('/docs', function () {
        throw new NotFoundHttpException();
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
            Route::get('', [GasController::class, 'get']);

            Route::get('odo', [GasController::class, 'getOdo']);

            Route::get('fuel', [GasController::class, 'getFuel']);
            Route::post('fuel', [GasController::class, 'addFuel']);
            Route::put('fuel/{id}', [GasController::class, 'editFuel']);
            Route::delete('fuel/{id}', [GasController::class, 'deleteFuel']);

            Route::get('maintenance', [GasController::class, 'getMaintenance']);
            Route::get('maintenance/parts', [GasController::class, 'getMaintenanceParts']);
            Route::post('maintenance', [GasController::class, 'addMaintenance']);
            Route::put('maintenance/{id}', [GasController::class, 'editMaintenance']);
            Route::delete('maintenance/{id}', [GasController::class, 'deleteMaintenance']);
          });

        Route::prefix('electricity')
          ->group(function () {
            Route::get('', [ElectricityController::class, 'get']);
            Route::post('', [ElectricityController::class, 'add']);
            Route::put('{id}', [ElectricityController::class, 'edit']);
            Route::delete('{id}', [ElectricityController::class, 'delete']);
          });

        Route::prefix('bills')
          ->group(function () {
            Route::prefix('electricity')
              ->group(function () {
                Route::get('', [BillsController::class, 'get']);
                Route::post('', [BillsController::class, 'add']);
                Route::put('{uuid}', [BillsController::class, 'edit']);
                Route::delete('{uuid}', [BillsController::class, 'delete']);
              });
          });
      });
  });

Route::prefix('api')
  ->middleware('api')
  ->namespace('App\Controllers')
  ->group(function () {

    Route::prefix('auth')
      ->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login'])->name('login');

        Route::middleware('auth:sanctum')
          ->group(function () {
            Route::get('user', [AuthController::class, 'getUser']);
            Route::post('logout', [AuthController::class, 'logout']);
          });
      });

    Route::middleware('auth:sanctum')
      ->group(function () {

        Route::get('logs', [LogController::class, 'index'])->middleware(IsAdminRole::class);
        Route::post('import', [ImportController::class, 'index'])->middleware(IsAdminRole::class);

        // Dropdowns
        Route::get('genres', [GenreController::class, 'index'])->middleware(IsAdminRole::class);
        Route::get('qualities', [QualityController::class, 'index'])->middleware(IsAdminRole::class);
        Route::get('priorities', [PriorityController::class, 'index'])->middleware(IsAdminRole::class);

        // All-in-one for Adding Entries
        // Groups + Qualities + Codecs + Genres + Watchers
        Route::get('dropdowns', [DropdownController::class, 'index'])->middleware(IsAdminRole::class);

        Route::prefix('management')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::get('', [ManagementController::class, 'index']);
            Route::get('by-year', [ManagementController::class, 'get_by_year']);
          });

        Route::prefix('users')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::get('', [UserController::class, 'index']);
            Route::get('{uuid}', [UserController::class, 'get']);
            Route::post('', [UserController::class, 'add']);
            Route::put('{uuid}', [UserController::class, 'edit']);
            Route::delete('{uuid}', [UserController::class, 'delete']);
          });

        // Non Admin Entry Routes
        Route::prefix('entries')
          ->group(function () {
            Route::get('', [EntryController::class, 'index']);
            Route::get('{uuid}', [EntryController::class, 'get']);
            Route::get('titles', [EntryController::class, 'getTitles']);
          });

        // Admin-required Entry Routes
        Route::prefix('entries')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::post('', [EntryController::class, 'add']);
            Route::put('{uuid}', [EntryController::class, 'edit']);
            Route::delete('{uuid}', [EntryController::class, 'delete']);
            Route::get('search', [EntryController::class, 'search']);
            Route::get('watchers', [EntryController::class, 'get_watchers']);
            Route::post('import', [EntryController::class, 'import']);

            Route::post('{uuid}/offquel/{uuid2}', [EntryController::class, 'add_offquel']);
            Route::delete('{uuid}/offquel/{uuid2}', [EntryController::class, 'delete_offquel']);
            Route::put('img-upload/{uuid}', [EntryController::class, 'imageUpload']);
            Route::delete('img-upload/{uuid}', [EntryController::class, 'imageDelete']);
            Route::put('ratings/{uuid}', [EntryController::class, 'ratings']);

            Route::post('rewatch/{uuid}', [EntryController::class, 'rewatchAdd']);
            Route::delete('rewatch/{uuid}', [EntryController::class, 'rewatchDelete']);

            Route::get('last', [EntryLastController::class, 'index']);

            Route::get('by-name', [EntryByNameController::class, 'index']);
            Route::get('by-name/{letter}', [EntryByNameController::class, 'get']);

            Route::get('by-year', [EntryByYearController::class, 'index']);
            Route::get('by-year/{year}', [EntryByYearController::class, 'get']);
            Route::get('by-year/uncategorized', [EntryByYearController::class, 'get']);

            Route::get('by-genre', [EntryByGenreController::class, 'index']);
            Route::get('by-genre/{string}', [EntryByGenreController::class, 'get']);

            Route::get('by-bucket', [EntryByBucketController::class, 'index']);
            Route::get('by-bucket/{id}', [EntryByBucketController::class, 'get']);

            Route::get('by-sequence/{id}', [EntryBySequenceController::class, 'index']);
          });

        Route::prefix('catalogs')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::get('', [CatalogController::class, 'index']);
            Route::post('', [CatalogController::class, 'add']);
            Route::put('{uuid}', [CatalogController::class, 'edit']);
            Route::delete('{uuid}', [CatalogController::class, 'delete']);

            Route::get('{uuid}/partials', [CatalogController::class, 'get']);
          });

        Route::prefix('partials')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::get('', [PartialController::class, 'index']);
            Route::get('{uuid}', [PartialController::class, 'get']);
            Route::post('', [PartialController::class, 'add']);
            Route::put('{uuid}', [PartialController::class, 'edit']);
            Route::delete('{uuid}', [PartialController::class, 'delete']);

            Route::post('multi', [PartialController::class, 'add_multiple']);
            Route::put('multi/{uuid}', [PartialController::class, 'edit_multiple']);
          });

        Route::prefix('buckets')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::post('import', [BucketController::class, 'import']);
          });

        Route::prefix('bucket-sims')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::get('', [BucketSimController::class, 'index']);
            Route::get('{uuid}', [BucketSimController::class, 'get']);
            Route::post('', [BucketSimController::class, 'add']);
            Route::put('{uuid}', [BucketSimController::class, 'edit']);
            Route::delete('{uuid}', [BucketSimController::class, 'delete']);

            Route::post('save/{uuid}', [BucketSimController::class, 'saveBucket']);
            Route::post('clone/{uuid}', [BucketSimController::class, 'clone']);
            Route::post('preview', [BucketSimController::class, 'preview']);
            Route::post('backup', [BucketSimController::class, 'backup']);
          });

        Route::prefix('sequences')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::get('', [SequenceController::class, 'index']);
            Route::get('{id}', [SequenceController::class, 'get']);
            Route::post('', [SequenceController::class, 'add']);
            Route::put('{id?}', [SequenceController::class, 'edit']);
            Route::delete('{id}', [SequenceController::class, 'delete']);
            Route::post('import', [SequenceController::class, 'import']);
          });

        Route::prefix('groups')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::get('', [GroupController::class, 'index']);
            Route::get('names', [GroupController::class, 'getNames']);
            Route::post('', [GroupController::class, 'add']);
            Route::put('{uuid}', [GroupController::class, 'edit']);
            Route::delete('{uuid}', [GroupController::class, 'delete']);
            Route::post('import', [GroupController::class, 'import']);
          });

        Route::prefix('codecs')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::get('', [CodecController::class, 'index']);

            Route::prefix('audio')
              ->group(function () {
                Route::get('', [CodecController::class, 'getAudio']);
                Route::post('', [CodecController::class, 'addAudio']);
                Route::put('{id}', [CodecController::class, 'editAudio']);
                Route::delete('{id}', [CodecController::class, 'deleteAudio']);
              });

            Route::prefix('video')
              ->group(function () {
                Route::get('', [CodecController::class, 'getVideo']);
                Route::post('', [CodecController::class, 'addVideo']);
                Route::put('{id}', [CodecController::class, 'editVideo']);
                Route::delete('{id}', [CodecController::class, 'deleteVideo']);
              });
          });

        Route::prefix('rss')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::get('', [RssController::class, 'index']);
            Route::get('{uuid}', [RssController::class, 'get']);
            Route::post('', [RssController::class, 'add']);
            Route::put('{uuid}', [RssController::class, 'edit']);
            Route::delete('{uuid}', [RssController::class, 'delete']);

            Route::put('read/{uuid}', [RssController::class, 'toggle_read']);
            Route::put('bookmark/{uuid}', [RssController::class, 'toggle_bookmark']);
          });

        Route::prefix('anilist')
          ->middleware(IsAdminRole::class)
          ->group(function () {
            Route::get('title/{integer}', [AnilistController::class, 'get']);
            Route::get('search', [AnilistController::class, 'search']);
          });

        Route::prefix('pc')
          ->middleware(IsAdminRole::class)
          ->group(function () {

            Route::post('import', [PCController::class, 'import']);

            Route::prefix('owners')
              ->group(function () {
                Route::get('', [PCOwnerController::class, 'index']);
                Route::get('{uuid}', [PCOwnerController::class, 'get']);
                Route::post('', [PCOwnerController::class, 'add']);
                Route::put('{uuid}', [PCOwnerController::class, 'edit']);
                Route::delete('{uuid}', [PCOwnerController::class, 'delete']);
                Route::post('import', [PCOwnerController::class, 'import']);
              });

            Route::prefix('infos')
              ->group(function () {
                Route::get('{uuid}', [PCInfoController::class, 'get']);
                Route::post('', [PCInfoController::class, 'add']);
                Route::put('{uuid}', [PCInfoController::class, 'edit']);
                Route::delete('{uuid}', [PCInfoController::class, 'delete']);
                Route::post('import', [PCInfoController::class, 'import']);

                Route::post('{uuid}/duplicate', [PCInfoController::class, 'duplicate']);
                Route::put('{uuid}/hide', [PCInfoController::class, 'toggle_hide']);
              });

            Route::prefix('components')
              ->group(function () {
                Route::get('', [PCComponentController::class, 'index']);
                Route::get('{id}', [PCComponentController::class, 'get']);
                Route::post('', [PCComponentController::class, 'add']);
                Route::put('{id}', [PCComponentController::class, 'edit']);
                Route::delete('{id}', [PCComponentController::class, 'delete']);
                Route::post('import', [PCComponentController::class, 'import']);
              });

            Route::prefix('setups')
              ->group(function () {
                Route::post('import', [PCSetupController::class, 'import']);
              });

            Route::prefix('types')
              ->group(function () {
                Route::get('', [PCComponentTypeController::class, 'index']);
                Route::post('', [PCComponentTypeController::class, 'add']);
                Route::put('{id}', [PCComponentTypeController::class, 'edit']);
                Route::delete('{id}', [PCComponentTypeController::class, 'delete']);
              });
          });
      });
  });
