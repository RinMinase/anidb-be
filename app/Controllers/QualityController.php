<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\QualityRepository;

class QualityController extends Controller {

  private QualityRepository $qualityRepository;

  public function __construct(QualityRepository $qualityRepository) {
    $this->qualityRepository = $qualityRepository;
  }


  /**
   * @api {get} /api/qualities Retrieve all quality
   * @apiName QualityRetrieve
   * @apiGroup Qualities
   *
   * @apiHeader {String} Authorization Token received from logging-in
   *
   * @apiSuccess {Object[]} data Quality Array
   * @apiSuccess {Number} data.id Quality ID
   * @apiSuccess {String} data.quality Quality description
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "data": [
   *         {
   *           "id": 1,
   *           "quality": "4K 2160p"
   *         }, { ... }
   *       ]
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->qualityRepository->getAll(),
    ]);
  }
}
