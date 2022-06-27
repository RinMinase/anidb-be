<?php

namespace App\Controllers;

use Exception;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

use App\Models\Sequence;

use App\Repositories\SequenceRepository;

class SequenceController extends Controller {

  private SequenceRepository $sequenceRepository;

  public function __construct(SequenceRepository $sequenceRepository) {
    $this->sequenceRepository = $sequenceRepository;
  }

  /**
   * @api {get} /api/sequences Retrieve all Sequences
   * @apiName SequenceRetrieve
   * @apiGroup Sequence
   *
   * @apiHeader {String} Authorization Token received from logging-in
   *
   * @apiSuccess {Object[]} data Sequence Data
   * @apiSuccess {Number} data.id ID of the sequence entry
   * @apiSuccess {String} data.title Descriptive title of the sequence
   * @apiSuccess {String} data.date_from Start date of the sequence
   * @apiSuccess {Number} data.date_to End date of the sequence
   * @apiSuccess {DateTime} data.created_at Creation date of the sequence entry
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "data": [
   *         {
   *           id: 1
   *           title: "Summer List",
   *           date_from: "2020-01-01",
   *           date_to: "2020-02-01",
   *           created_at: "2020-01-01 00:00:00",
   *         }
   *       ]
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->sequenceRepository->getAll(),
    ]);
  }

  public function get($id): JsonResponse {
    return response()->json([
      'data' => $this->sequenceRepository->get($id),
    ]);
  }

  public function add(Request $request): JsonResponse {
    try {
      $this->sequenceRepository->add($request->all());

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (Exception) {
      return response()->json([
        'status' => 500,
        'message' => 'Failed',
      ], 500);
    }
  }

  public function edit(Request $request, $id): JsonResponse {
    try {
      $this->sequenceRepository->edit(
        $request->except(['_method']),
        $id
      );

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (Exception) {
      return response()->json([
        'status' => 500,
        'message' => 'Failed',
      ], 500);
    }
  }

  public function delete($id): JsonResponse {
    try {
      $this->sequenceRepository->delete($id);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'Catalog ID does not exist',
      ], 401);
    }
  }

  public function import(Request $request) {
    try {
      $import = [];

      foreach ($request->all() as $item) {
        if (!empty($item)) {
          $data = [
            'date_from' => Carbon::createFromTimestamp($item['timeStart'])
              ->format('Y-m-d'),
            'date_to' => Carbon::createFromTimestamp($item['timeEnd'])
              ->format('Y-m-d'),
            'title' => $item['title'],

            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
          ];

          array_push($import, $data);
        }
      }

      Sequence::insert($import);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
        'data' => [
          'acceptedImports' => count($import),
          'totalJsonEntries' => count($request->all()),
        ],
      ]);
    } catch (Exception) {
      return response()->json([
        'status' => 401,
        'message' => 'Failed to import JSON file',
      ]);
    }
  }
}
