<?php

namespace App\Controllers;

use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

use Kreait\Firebase\Factory as FirebaseFactory;
use Kreait\Firebase\ServiceAccount as FirebaseServiceAcct;

class ImageController extends Controller {

  protected $firebase;

  public function __construct() {
    $creds = json_encode([
      'project_id' => env('FIRE_PROJECT_ID', ''),
      'private_key' => env('FIRE_KEY', ''),
      'client_email' => env('FIRE_EMAIL', ''),
      'client_id' => env('FIRE_CLIENT_ID', ''),
      'type' => 'service_account',
    ]);

    $validatedCreds = str_replace('\\\\n', '\\n', $creds);

    $this->firebase = (new FirebaseFactory)
      ->withServiceAccount(FirebaseServiceAcct::fromValue($validatedCreds))
      ->withDisabledAutoDiscovery()
      ->createStorage();
  }

  /**
   * @api {get} /api/img/:param Retrieve Image URL
   * @apiName ImageRetrieve
   * @apiGroup Image
   *
   * @apiHeader {String} Authorization Token received from logging-in
   * @apiParam {Path} param Complete image file path (i.e. 'assets/test.jpg')
   *
   * @apiSuccess {String} url Signed URL of image
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "url": "https://storage.googleapis.com/example.appspot.com/assets/test.jpg?X-Goog-Algorithm=GOOG4-RSA-SHA256&X-Goog-Credential=example%40appspot.gserviceaccount.com{{url and key contents}}"
   *     }
   *
   * @apiError Invalid The specified image path does not exist
   *
   * @apiErrorExample Invalid
   *     HTTP/1.1 400 Bad Request
   *     {
   *       "status": "Invalid",
   *       "message": "Image path is invalid"
   *     }
   */
  public function index($params): JsonResponse {
    if (!env('DISABLE_FIREBASE')) {
      $hasCredentials = env('FIRE_PROJECT_ID')
        && env('FIRE_KEY')
        && env('FIRE_EMAIL')
        && env('FIRE_CLIENT_ID');

      if ($hasCredentials) {
        return $this->retrieve($params);
      } else {
        return response()->json([
          'status' => 500,
          'message' => 'Firebase configuration not found',
        ], 500);
      }
    } else {
      return response()->json([
        'status' => 500,
        'message' => 'Firebase is disabled',
      ], 500);
    }
  }

  private function retrieve($params) {
    $url = $this->firebase
      ->getBucket()
      ->object(urldecode($params))
      ->signedUrl(new DateTime('tomorrow'), ['version' => 'v4']);

    $data = $this->verifyImageContents($url);
    $statusCode = (array_key_exists('Status', $data)) ? 400 : 200;

    return response()->json($data, $statusCode);
  }

  private function verifyImageContents($url) {
    $invalidMsg = [
      'status' => 'Invalid',
      'message' => 'Image path is invalid',
    ];

    try {
      $type = Http::get($url)->header('content-type');
      $data = (strpos($type, 'image') !== false) ? ['url' => $url] : $invalidMsg;
    } catch (Exception) {
      $data = $invalidMsg;
    }

    return $data;
  }
}
