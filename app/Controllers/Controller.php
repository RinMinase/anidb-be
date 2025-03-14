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
 * @OA\Tag(name="AniList")
 * @OA\Tag(name="Bucket Simulation")
 * @OA\Tag(name="Catalog")
 * @OA\Tag(name="Codec")
 * @OA\Tag(name="Dropdowns")
 * @OA\Tag(name="Entry")
 * @OA\Tag(name="Entry Specific")
 * @OA\Tag(name="Group")
 * @OA\Tag(name="Import")
 * @OA\Tag(name="Import - Archaic")
 * @OA\Tag(name="Logs")
 * @OA\Tag(name="Management")
 * @OA\Tag(name="Sequence")
 * @OA\Tag(name="PC")
 * @OA\Tag(name="Fourleaf - Gas")
 * @OA\Tag(name="Fourleaf - Electricity")
 * @OA\Tag(name="Fourleaf - Bills")
 */
class Controller extends BaseController {
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

/**
 * @OA\Schema(
 *   schema="Pagination",
 *   title="Pagination Meta",
 *   @OA\Property(
 *     property="meta",
 *     @OA\Property(
 *       property="page",
 *       type="integer",
 *       format="int32",
 *       minimum=1,
 *       example=1,
 *     ),
 *     @OA\Property(
 *       property="limit",
 *       type="integer",
 *       format="int32",
 *       minimum=1,
 *       example=30,
 *     ),
 *     @OA\Property(
 *       property="results",
 *       type="integer",
 *       format="int32",
 *       minimum=0,
 *       example=30,
 *     ),
 *     @OA\Property(
 *       property="total_results",
 *       type="integer",
 *       format="int32",
 *       minimum=0,
 *       example=130,
 *     ),
 *     @OA\Property(
 *       property="total_pages",
 *       type="integer",
 *       format="int32",
 *       minimum=1,
 *       example=5,
 *     ),
 *     @OA\Property(property="has_next", type="boolean", example=true),
 *   ),
 * )
 */
class PaginationMeta {
}

/**
 * @OA\Schema(
 *   schema="YearSchema",
 *   title="Year Schema",
 *   type="integer",
 *   format="int32",
 *   minimum=1970,
 *   maximum=2999,
 *   example=2020,
 * ),
 */
class YearSchema {
}


/**
 * @OA\Schema(
 *   schema="DefaultImportSchema",
 *   title="Default Import Schema",
 *   @OA\Property(
 *     property="acceptedImports",
 *     type="integer",
 *     format="int32",
 *     example=0,
 *   ),
 *   @OA\Property(
 *     property="totalJsonEntries",
 *     type="integer",
 *     format="int32",
 *     example=0,
 *   ),
 * ),
 */
class DefaultImportSchema {
}
