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
use App\Controllers\EntryByBucketController;
use App\Controllers\EntryByNameController;
use App\Controllers\EntryBySequenceController;
use App\Controllers\EntryByYearController;
use App\Controllers\EntryController;
use App\Controllers\EntryLastController;
use App\Controllers\GroupController;
use App\Controllers\ImportController;
use App\Controllers\LogController;
use App\Controllers\MALController;
use App\Controllers\ManagementController;
use App\Controllers\PartialController;
use App\Controllers\PCSetupController;
use App\Controllers\PriorityController;
use App\Controllers\QualityController;
use App\Controllers\ReleaseController;
use App\Controllers\RssController;
use App\Controllers\SequenceController;

use App\Fourleaf\Controllers\ElectricityController;
use App\Fourleaf\Controllers\GasController;

Route::pattern('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
Route::pattern('integer', '[0-9]+');
Route::pattern('string', '[a-z]+');
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

        Route::get('management', [ManagementController::class, 'index']);
        Route::get('logs', [LogController::class, 'index']);
        Route::get('qualities', [QualityController::class, 'index']);
        Route::get('priorities', [PriorityController::class, 'index']);
        Route::post('import', [ImportController::class, 'index']);

        Route::prefix('entries')
          ->group(function () {
            Route::get('', [EntryController::class, 'index']);
            Route::get('{uuid}', [EntryController::class, 'get']);
            Route::post('', [EntryController::class, 'add']);
            Route::put('{uuid}', [EntryController::class, 'edit']);
            Route::delete('{uuid}', [EntryController::class, 'delete']);
            Route::get('search', [EntryController::class, 'search']);
            Route::post('import', [EntryController::class, 'import']);

            Route::put('img-upload/{uuid}', [EntryController::class, 'imageUpload']);
            Route::delete('img-upload/{uuid}', [EntryController::class, 'imageDelete']);
            Route::put('ratings/{uuid}', [EntryController::class, 'ratings']);
            Route::get('titles', [EntryController::class, 'getTitles']);

            Route::post('rewatch/{uuid}', [EntryController::class, 'rewatchAdd']);
            Route::delete('rewatch/{uuid}', [EntryController::class, 'rewatchDelete']);

            Route::get('last', [EntryLastController::class, 'index']);

            Route::get('by-name', [EntryByNameController::class, 'index']);
            Route::get('by-name/{letter}', [EntryByNameController::class, 'get']);

            Route::get('by-year', [EntryByYearController::class, 'index']);
            Route::get('by-year/{year}', [EntryByYearController::class, 'get']);
            Route::get('by-year/uncategorized', [EntryByYearController::class, 'get']);

            Route::get('by-bucket', [EntryByBucketController::class, 'index']);
            Route::get('by-bucket/{id}', [EntryByBucketController::class, 'get']);

            Route::get('by-sequence/{id}', [EntryBySequenceController::class, 'index']);
          });

        Route::prefix('catalogs')
          ->group(function () {
            Route::get('', [CatalogController::class, 'index']);
            Route::post('', [CatalogController::class, 'add']);
            Route::put('{uuid}', [CatalogController::class, 'edit']);
            Route::delete('{uuid}', [CatalogController::class, 'delete']);

            Route::get('{uuid}/partials', [CatalogController::class, 'get']);
          });

        Route::prefix('partials')
          ->group(function () {
            Route::get('{uuid}', [PartialController::class, 'get']);
            Route::post('', [PartialController::class, 'add']);
            Route::put('{uuid}', [PartialController::class, 'edit']);
            Route::delete('{uuid}', [PartialController::class, 'delete']);

            Route::post('multi', [PartialController::class, 'add_multiple']);
            Route::put('multi/{uuid}', [PartialController::class, 'edit_multiple']);
          });

        Route::prefix('buckets')
          ->group(function () {

            // ======= Unused =======
            // Route::get('', [BucketController::class, 'index']);
            // Route::post('', [BucketController::class, 'add']);
            // Route::put('{id?}', [BucketController::class, 'edit']);
            // Route::delete('{id}', [BucketController::class, 'delete']);
            // ======================

            Route::post('import', [BucketController::class, 'import']);
          });

        Route::prefix('bucket-sims')
          ->group(function () {
            Route::get('', [BucketSimController::class, 'index']);
            Route::get('{uuid}', [BucketSimController::class, 'get']);
            Route::post('', [BucketSimController::class, 'add']);
            Route::put('{uuid}', [BucketSimController::class, 'edit']);
            Route::delete('{uuid}', [BucketSimController::class, 'delete']);

            Route::post('{uuid}', [BucketSimController::class, 'saveBucket']);
          });

        Route::prefix('sequences')
          ->group(function () {
            Route::get('', [SequenceController::class, 'index']);
            Route::get('{id}', [SequenceController::class, 'get']);
            Route::post('', [SequenceController::class, 'add']);
            Route::put('{id?}', [SequenceController::class, 'edit']);
            Route::delete('{id}', [SequenceController::class, 'delete']);
            Route::post('import', [SequenceController::class, 'import']);
          });

        Route::prefix('groups')
          ->group(function () {
            Route::get('', [GroupController::class, 'index']);
            Route::get('names', [GroupController::class, 'getNames']);
            Route::post('', [GroupController::class, 'add']);
            Route::put('{uuid}', [GroupController::class, 'edit']);
            Route::delete('{uuid}', [GroupController::class, 'delete']);
            Route::post('import', [GroupController::class, 'import']);
          });

        Route::prefix('codecs')
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
          ->group(function () {
            Route::get('', [RssController::class, 'index']);
            Route::get('{uuid}', [RssController::class, 'get']);
            Route::post('', [RssController::class, 'add']);
            Route::put('{uuid}', [RssController::class, 'edit']);
            Route::delete('{uuid}', [RssController::class, 'delete']);

            Route::put('read/{uuid}', [RssController::class, 'read']);
            Route::delete('read/{uuid}', [RssController::class, 'unread']);

            Route::put('bookmark/{uuid}', [RssController::class, 'bookmark']);
            Route::delete('bookmark/{uuid}', [RssController::class, 'removeBookmark']);
          });

        Route::prefix('anilist')
          ->group(function () {
            Route::get('title/{integer}', [AnilistController::class, 'get']);
            Route::get('search', [AnilistController::class, 'search']);
          });

        Route::prefix('pc-setups')
          ->group(function () {
            Route::get('', [PCSetupController::class, 'index']);
            Route::get('{id}', [PCSetupController::class, 'get']);
            Route::post('', [PCSetupController::class, 'add']);
            Route::put('{id}', [PCSetupController::class, 'edit']);
            Route::delete('{id}', [PCSetupController::class, 'delete']);
            Route::post('import', [PCSetupController::class, 'import']);

            Route::post('duplicate/{id}', [PCSetupController::class, 'duplicate']);
            Route::put('current/{id}', [PCSetupController::class, 'toggleCurrent']);
            Route::put('future/{id}', [PCSetupController::class, 'toggleFuture']);
            Route::put('server/{id}', [PCSetupController::class, 'toggleServer']);
          });

        // ===== Deprecated =====
        Route::prefix('mal')
          ->group(function () {
            Route::get('title/{integer}', [MALController::class, 'get']);
            Route::get('search/{string}', [MALController::class, 'search']);
          });

        Route::get('changelog/{params?}', [ReleaseController::class, 'getLogs']);
        Route::get('changelog-be/{params?}', [ReleaseController::class, 'getLogsBE']);
        Route::get('issues/{params?}', [ReleaseController::class, 'getIssues']);
        // ======================
      });
  });
