<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repositories\RssRepository;
use App\Resources\Rss\RssCollection;

class RssController extends Controller {

  private RssRepository $rssRepository;

  public function __construct(RssRepository $rssRepository) {
    $this->rssRepository = $rssRepository;
  }

  /**
   * @OA\Get(
   *   tags={"RSS"},
   *   path="/api/rss",
   *   summary="Get All RSS Feeds",
   *   security={{"token":{}}},
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(ref="#/components/schemas/RssCollection"),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function index(): JsonResponse {
    try {
      $data = $this->rssRepository->getAll();
      $data = RssCollection::collection($data);

      return response()->json($data);
    } catch (Exception) {
      return response()->json([
        'status' => 500,
        'message' => 'Failed',
      ], 500);
    }
  }

  /**
   * @OA\Get(
   *   tags={"RSS"},
   *   path="/api/rss/{rss_feed_id}",
   *   summary="Get RSS Items in RSS Feed",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="rss_feed_id",
   *     description="RSS Feed ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       type="array",
   *       @OA\Items(ref="#/components/schemas/RssItem"),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function get($uuid): JsonResponse {
    try {
      // update rss feed, clear garbage, get item list, return list
      $data = $this->rssRepository->get($uuid);

      return response()->json($data);
    } catch (Exception) {
      return response()->json([
        'status' => 500,
        'message' => 'Failed',
      ], 500);
    }
  }

  /**
   * @OA\Post(
   *   tags={"RSS"},
   *   path="/api/rss",
   *   summary="Add an RSS Feed",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="title",
   *     in="query",
   *     required=true,
   *     example="Sample RSS Feed",
   *     @OA\Schema(type="string"),
   *   ),
   *   @OA\Parameter(
   *     name="update_speed_mins",
   *     in="query",
   *     required=true,
   *     example=60,
   *     @OA\Schema(type="integer", format="int32", default=60),
   *   ),
   *   @OA\Parameter(
   *     name="url",
   *     in="query",
   *     required=true,
   *     example="https://example.com/",
   *     @OA\Schema(type="string", format="uri"),
   *   ),
   *   @OA\Parameter(
   *     name="max_items",
   *     in="query",
   *     required=true,
   *     example=250,
   *     @OA\Schema(type="integer", format="int32", default=250),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function add(Request $request): JsonResponse {
    try {
      $this->rssRepository->add($request->all());

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

  /**
   * @OA\Put(
   *   tags={"RSS"},
   *   path="/api/rss/{rss_feed_id}",
   *   summary="Edit an RSS Feed",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="rss_feed_id",
   *     description="RSS Feed ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *   @OA\Parameter(
   *     name="title",
   *     in="query",
   *     required=true,
   *     example="Sample RSS Feed",
   *     @OA\Schema(type="string"),
   *   ),
   *   @OA\Parameter(
   *     name="update_speed_mins",
   *     in="query",
   *     required=true,
   *     example=60,
   *     @OA\Schema(type="integer", format="int32", default=60),
   *   ),
   *   @OA\Parameter(
   *     name="url",
   *     in="query",
   *     required=true,
   *     example="https://example.com/",
   *     @OA\Schema(type="string", format="uri"),
   *   ),
   *   @OA\Parameter(
   *     name="max_items",
   *     in="query",
   *     required=true,
   *     example=250,
   *     @OA\Schema(type="integer", format="int32", default=250),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function edit(Request $request, $uuid): JsonResponse {
    try {
      $this->rssRepository->edit($request->except(['_method']), $uuid);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'RSS ID does not exist',
      ], 401);
    }
  }

  /**
   * @OA\Delete(
   *   tags={"RSS"},
   *   path="/api/rss/{rss_feed_id}",
   *   summary="Delete an RSS Feed",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="rss_feed_id",
   *     description="RSS Feed ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function delete($uuid): JsonResponse {
    try {
      $this->rssRepository->delete($uuid);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'RSS ID does not exist',
      ], 401);
    }
  }

  /**
   * @OA\Post(
   *   tags={"RSS"},
   *   path="/api/rss/read/{rss_item_id}",
   *   summary="Mark an RSS Item as Read",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="rss_item_id",
   *     description="RSS Item ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function read($uuid): JsonResponse {
    try {
      $this->rssRepository->read($uuid);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'RSS Item ID does not exist',
      ], 401);
    }
  }

  /**
   * @OA\Delete(
   *   tags={"RSS"},
   *   path="/api/rss/read/{rss_item_id}",
   *   summary="Mark an RSS Item as Unread",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="rss_item_id",
   *     description="RSS Item ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function unread($uuid): JsonResponse {
    try {
      $this->rssRepository->unread($uuid);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'RSS Item ID does not exist',
      ], 401);
    }
  }

  /**
   * @OA\Post(
   *   tags={"RSS"},
   *   path="/api/rss/bookmark/{rss_item_id}",
   *   summary="Bookmark an RSS Item",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="rss_item_id",
   *     description="RSS Item ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function bookmark($uuid): JsonResponse {
    try {
      $this->rssRepository->bookmark($uuid);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'RSS Item ID does not exist',
      ], 401);
    }
  }

  /**
   * @OA\Delete(
   *   tags={"RSS"},
   *   path="/api/rss/bookmark/{rss_item_id}",
   *   summary="Delete an RSS Item from Bookmarks",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(
   *     name="rss_item_id",
   *     description="RSS Item ID",
   *     in="path",
   *     required=true,
   *     example="e9597119-8452-4f2b-96d8-f2b1b1d2f158",
   *     @OA\Schema(type="string", format="uuid"),
   *   ),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   * )
   */
  public function removeBookmark($uuid): JsonResponse {
    try {
      $this->rssRepository->removeBookmark($uuid);

      return response()->json([
        'status' => 200,
        'message' => 'Success',
      ]);
    } catch (ModelNotFoundException) {
      return response()->json([
        'status' => 401,
        'message' => 'RSS Item ID does not exist',
      ], 401);
    }
  }
}
