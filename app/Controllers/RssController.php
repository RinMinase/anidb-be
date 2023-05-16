<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Repositories\RssRepository;

class RssController extends Controller {

  private RssRepository $rssRepository;

  public function __construct(RssRepository $rssRepository) {
    $this->rssRepository = $rssRepository;
  }

  public function index(): JsonResponse {
    try {
      $data = $this->rssRepository->getAll();

      return response()->json($data);
    } catch (Exception) {
      return response()->json([
        'status' => 500,
        'message' => 'Failed',
      ], 500);
    }
  }

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
