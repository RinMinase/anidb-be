<?php

namespace App\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="AniDB API Documentation"
 * ),
 */
class Controller extends BaseController {
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

/**
 * @OA\Schema(
 *   schema="Success",
 *   title="Default Success Response",
 *   example={"status": "200", "message": "Success"},
 *   @OA\Property(property="status", type="string"),
 *   @OA\Property(property="message", type="string"),
 * )
 */
class SuccessResponse {
}

/**
 * @OA\Schema(
 *   schema="Unauthorized",
 *   title="401 Forbidden Error Response",
 *   example={"status": "401", "message": "Unauthorized"},
 *   @OA\Property(property="status", type="string"),
 *   @OA\Property(property="message", type="string"),
 * )
 */
class UnauthorizedResponse {
}

/**
 * @OA\Schema(
 *   schema="Failed",
 *   title="500 Internal Server Error Response",
 *   example={"status": "500", "message": "Failed"},
 *   @OA\Property(property="status", type="string"),
 *   @OA\Property(property="message", type="string"),
 * )
 */
class FailedResponse {
}
