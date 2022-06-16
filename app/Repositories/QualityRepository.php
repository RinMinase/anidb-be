<?php

namespace App\Repositories;

use App\Models\Quality;

class QualityRepository {

  /**
   * @api {get} /api/quality Retrieve all quality
   * @apiName QualityRetrieve
   * @apiGroup Qualities
   *
   * @apiHeader {String} Authorization Token received from logging-in
   *
   * @apiSuccess {String[]} data Quality Array
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "data": [
   *         "4K 2160p"
   *         "FHD 1080p"
   *       ]
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   *
   * @apiErrorExample Unauthorized
   *     HTTP/1.1 401 Forbidden
   *     {
   *       "status": "Unauthorized",
   *       "message": "Unauthorized"
   *     }
   */
  public function getAll() {
    return Quality::all();
  }
}
