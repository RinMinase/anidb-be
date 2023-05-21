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
 * @OA\SecurityScheme(
 *   securityScheme="token",
 *   description="Login with email and password to get the authentication token",
 *   in="header",
 *   name="Authorization",
 *   type="http",
 *   bearerFormat="JWT",
 *   scheme="bearer",
 * ),
 */
class Controller extends BaseController {
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

/**
 * @OA\Response(
 *   response="Success",
 *   description="Success",
 *   @OA\JsonContent(
 *     example={"status": 200, "message": "Success"},
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(property="message", type="string"),
 *   ),
 * )
 */
class SuccessResponse {
}


/**
 * @OA\Response(
 *   response="Unauthorized",
 *   description="Unauthorized",
 *   @OA\JsonContent(
 *     example={"status": 401, "message": "Unauthorized"},
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(property="message", type="string"),
 *   ),
 * )
 */
class UnauthorizedResponse {
}

/**
 * @OA\Response(
 *   response="Failed",
 *   description="Failed",
 *   @OA\JsonContent(
 *     example={"status": 500, "message": "Failed"},
 *     @OA\Property(property="status", type="integer", format="int32"),
 *     @OA\Property(property="message", type="string"),
 *   ),
 * )
 */
class FailedResponse {
}

/**
 * @OA\Schema(
 *   schema="Pagination",
 *   title="Pagination Meta",
 *   example={
 *     "meta": {
 *       "page": 1,
 *       "limit": 30,
 *       "total": 5,
 *       "has_next": true,
 *     }
 *   },
 *   @OA\Property(
 *     property="meta",
 *     type="object",
 *     @OA\Property(property="page", type="integer", format="int32", minimum=1),
 *     @OA\Property(property="limit", type="integer", format="int32", minimum=1),
 *     @OA\Property(property="total", type="integer", format="int32", minimum=1),
 *     @OA\Property(property="has_next", type="boolean"),
 *   ),
 * )
 */
class PaginationMeta {
}
