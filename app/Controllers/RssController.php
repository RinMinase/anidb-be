<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;

use App\Repositories\RssRepository;

use App\Requests\Rss\AddEditRequest;

use App\Resources\Rss\RssCollection;
use App\Resources\DefaultResponse;

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
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function index(): JsonResponse {
    $data = $this->rssRepository->getAll();
    $data = RssCollection::collection($data);

    return response()->json($data);
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
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function get($uuid): JsonResponse {
    // update rss feed, clear garbage, get item list, return list
    $data = $this->rssRepository->get($uuid);

    return response()->json($data);
  }

  /**
   * @OA\Post(
   *   tags={"RSS"},
   *   path="/api/rss",
   *   summary="Add an RSS Feed",
   *   security={{"token":{}}},
   *
   *   @OA\Parameter(ref="#/components/parameters/rss_add_edit_title"),
   *   @OA\Parameter(ref="#/components/parameters/rss_add_edit_update_speed_mins"),
   *   @OA\Parameter(ref="#/components/parameters/rss_add_edit_url"),
   *   @OA\Parameter(ref="#/components/parameters/rss_add_edit_max_items"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(AddEditRequest $request): JsonResponse {
    $this->rssRepository->add(
      $request->only('title', 'update_speed_mins', 'url', 'max_items'),
    );

    return DefaultResponse::success();
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
   *   @OA\Parameter(ref="#/components/parameters/rss_add_edit_title"),
   *   @OA\Parameter(ref="#/components/parameters/rss_add_edit_update_speed_mins"),
   *   @OA\Parameter(ref="#/components/parameters/rss_add_edit_url"),
   *   @OA\Parameter(ref="#/components/parameters/rss_add_edit_max_items"),
   *
   *   @OA\Response(response=200, ref="#/components/responses/Success"),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit(AddEditRequest $request, $uuid): JsonResponse {
    $this->rssRepository->edit(
      $request->only('title', 'update_speed_mins', 'url', 'max_items'),
      $uuid,
    );

    return DefaultResponse::success();
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
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function delete($uuid): JsonResponse {
    $this->rssRepository->delete($uuid);

    return DefaultResponse::success();
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
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function read($uuid): JsonResponse {
    $this->rssRepository->read($uuid);

    return DefaultResponse::success();
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
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function unread($uuid): JsonResponse {
    $this->rssRepository->unread($uuid);

    return DefaultResponse::success();
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
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function bookmark($uuid): JsonResponse {
    $this->rssRepository->bookmark($uuid);

    return DefaultResponse::success();
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
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function removeBookmark($uuid): JsonResponse {
    $this->rssRepository->removeBookmark($uuid);

    return DefaultResponse::success();
  }
}
