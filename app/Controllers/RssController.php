<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Repositories\RssRepository;
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
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function add(Request $request): JsonResponse {
    $this->rssRepository->add($request->all());

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
   *   @OA\Response(response=404, ref="#/components/responses/NotFound"),
   *   @OA\Response(response=500, ref="#/components/responses/Failed"),
   * )
   */
  public function edit(Request $request, $uuid): JsonResponse {
    $this->rssRepository->edit($request->except(['_method']), $uuid);

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
