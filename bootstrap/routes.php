<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'index');

Route::prefix('api')
  ->namespace('App\Controllers')
  ->group(function () {

    Route::get('img/{params}', 'ImageController@index')
      ->where('params', '.*');

    Route::get('/mal/{params?}', 'MalController@index');

    Route::get('/changelog/{params?}', 'ReleaseController@getLogs');
    Route::get('/changelog-be/{params?}', 'ReleaseController@getLogsBE');
    Route::get('/issues/{params?}', 'ReleaseController@getIssues');

    Route::get('/hdd', 'HddController@index');
  });

Route::middleware('auth:sanctum')
  ->get('/user', function (Request $request) {
    return $request->user();
  });
