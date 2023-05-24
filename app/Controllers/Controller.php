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
 *
 * @OA\SecurityScheme(
 *   securityScheme="token",
 *   description="Login with email and password to get the authentication token",
 *   in="header",
 *   name="Authorization",
 *   type="http",
 *   bearerFormat="JWT",
 *   scheme="bearer",
 * ),
 *
 * // For Ordering Purposes
 * @OA\Tag(name="User")
 * @OA\Tag(name="Bucket")
 * @OA\Tag(name="BucketSim")
 * @OA\Tag(name="Catalog")
 * @OA\Tag(name="Codec")
 * @OA\Tag(name="Dropdowns")
 * @OA\Tag(name="Entry")
 * @OA\Tag(name="Entry Specific")
 * @OA\Tag(name="Group")
 * @OA\Tag(name="Logs")
 * @OA\Tag(name="MAL")
 * @OA\Tag(name="Management")
 * @OA\Tag(name="RSS")
 * @OA\Tag(name="Sequence")
 * @OA\Tag(name="Release")
 */
class Controller extends BaseController {
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
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
