<?php

namespace App\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController {
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


  /**
   * @api {get} /api Unauthorized
   * @apiName ErrorsUnauthorized
   * @apiGroup ❌ Errors ❌
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   *
   * @apiErrorExample Unauthorized
   *     HTTP/1.1 401 Forbidden
   *     {
   *       "status": "401",
   *       "message": "Unauthorized"
   *     }
   */


  /**
   * @api {get} /api Failed
   * @apiName ErrorsFailed
   * @apiGroup ❌ Errors ❌
   *
   * @apiError Failed Some kind of error has happened
   *
   * @apiErrorExample Failed
   *     HTTP/1.1 500 Internal Server Error
   *     {
   *       "status": "500",
   *       "message": "Failed"
   *     }
   */


  /**
   * @api {get} /api Default Success Response
   * @apiName SuccessDefault
   * @apiGroup ✅ Success ✅
   *
   * @apiSuccess {Number} status Status code
   * @apiSuccess {String} message Message
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "status": 200,
   *       "message": "Success",
   *     }
   */
}
