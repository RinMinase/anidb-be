<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\SequenceRepository;

class SequenceController extends Controller {

  private SequenceRepository $sequenceRepository;

  public function __construct(SequenceRepository $sequenceRepository) {
    $this->sequenceRepository = $sequenceRepository;
  }

  /**
   * @api {get} /api/sequence Retrieve all Sequences
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
}
