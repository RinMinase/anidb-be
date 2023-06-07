<?php

namespace App\Controllers;

use DateTime;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

use App\Resources\ErrorResponse;

class ReleaseController extends Controller {

  protected $feReleaseURI;
  protected $beReleaseURI;

  public function __construct() {
    if ($this->isScraperEnabled()) {
      $this->feReleaseURI = config('app.scraper.release_base_uri') . '/';
      $this->beReleaseURI = config('app.scraper.release_base_uri') . '-be/';
    }
  }

  /**
   * @OA\Get(
   *   tags={"Release"},
   *   path="/api/changelog/{limit}",
   *   summary="Retrieve Frontend Changlog",
   *   security={{"token":{}}},
   *   deprecated=true,
   *
   *   @OA\Parameter(
   *     name="limit",
   *     description="Changelog Count Limit",
   *     in="path",
   *     example="20",
   *     @OA\Schema(type="integer", format="int32", default=20),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(
   *         property="changes_{{date}}",
   *         @OA\Property(property="title", type="string"),
   *         @OA\Property(property="dep", ref="#/components/schemas/ChangelogItem"),
   *         @OA\Property(property="fix", ref="#/components/schemas/ChangelogItem"),
   *         @OA\Property(property="new", ref="#/components/schemas/ChangelogItem"),
   *         @OA\Property(property="improve", ref="#/components/schemas/ChangelogItem"),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/ReleaseConfigErrorResponse"),
   * )
   */
  public function getLogs($limit = 20): JsonResponse {
    if ($this->isScraperEnabled()) {
      $data = Http::get($this->feReleaseURI . 'commits', [
        'query' => [
          'per_page' => intval($limit) || 20,
        ],
      ])->body();

      $data = $this->parseChangelog($data);

      return response($data)
        ->header('Content-Type', 'application/json');
    }
  }

  /**
   * @OA\Get(
   *   tags={"Release"},
   *   path="/api/changelog-be/{limit}",
   *   summary="Retrieve Backend Changlog",
   *   security={{"token":{}}},
   *   deprecated=true,
   *
   *   @OA\Parameter(
   *     name="limit",
   *     description="Changelog Count Limit",
   *     in="path",
   *     example="20",
   *     @OA\Schema(type="integer", format="int32", default=20),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\JsonContent(
   *       @OA\Property(
   *         property="changes_{{date}}",
   *         @OA\Property(property="title", type="string"),
   *         @OA\Property(property="dep", ref="#/components/schemas/ChangelogItem"),
   *         @OA\Property(property="fix", ref="#/components/schemas/ChangelogItem"),
   *         @OA\Property(property="new", ref="#/components/schemas/ChangelogItem"),
   *         @OA\Property(property="improve", ref="#/components/schemas/ChangelogItem"),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/ReleaseConfigErrorResponse"),
   * )
   */
  public function getLogsBE($limit = 20): JsonResponse {
    if ($this->isScraperEnabled()) {
      $data = Http::get($this->beReleaseURI . 'commits', [
        'query' => [
          'per_page' => intval($limit) || 20,
        ],
      ])->body();

      $data = $this->parseChangelog($data);

      return response($data)
        ->header('Content-Type', 'application/json');
    }
  }

  /**
   * @OA\Get(
   *   tags={"Release"},
   *   path="/api/issues/{limit}",
   *   summary="Retrieve GitHub Issues",
   *   security={{"token":{}}},
   *   deprecated=true,
   *
   *   @OA\Parameter(
   *     name="limit",
   *     description="Issue Count Limit",
   *     in="path",
   *     example="100",
   *     @OA\Schema(type="integer", format="int64", default=100),
   *   ),
   *
   *   @OA\Response(
   *     response=200,
   *     description="Success",
   *     @OA\Schema(
   *       example={{
   *         "date": "Jan 01, 2020",
   *         "number": 100,
   *         "title": "This is a sample issue title",
   *         "url": "{{ GitHub issue URL }}",
   *         "labels": {{
   *           "class": "type-enhancement",
   *           "name": "ENHANCEMENT",
   *         }},
   *       }},
   *       type="array",
   *       @OA\Items(
   *         @OA\Property(property="date", type="string"),
   *         @OA\Property(property="number", type="interger", format="int32"),
   *         @OA\Property(property="title", type="string"),
   *         @OA\Property(property="url", type="string", format="uri"),
   *         @OA\Property(
   *           property="labels",
   *           type="array",
   *           @OA\Items(
   *             @OA\Property(property="class", type="string"),
   *             @OA\Property(property="name", type="string"),
   *           ),
   *         ),
   *       ),
   *     ),
   *   ),
   *   @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
   *   @OA\Response(response=500, ref="#/components/responses/ReleaseConfigErrorResponse"),
   * )
   */
  public function getIssues($limit = 100): JsonResponse {
    if ($this->isScraperEnabled()) {
      $data = Http::get($this->beReleaseURI . 'issues', [
        'query' => [
          'per_page' => intval($limit) || 100,
          'page' => 1,
        ],
      ])->body();

      $data = $this->parseIssues($data);

      return response($data)
        ->header('Content-Type', 'application/json');
    }
  }

  /**
   * @OA\Response(
   *   response="ReleaseConfigErrorResponse",
   *   description="Failed Request or Release URL Configuration Error Responses",
   *   @OA\JsonContent(
   *     examples={
   *       @OA\Examples(
   *         summary="Release URL Not Found",
   *         example="ReleaseUrlConfigNotFound",
   *         value={"status": 500, "message": "Release URL configuration not found"},
   *       ),
   *       @OA\Examples(
   *         summary="Release Scraper Disabled",
   *         example="ReleaseScraperDisabled",
   *         value={"status": 500, "message": "Release URL / Web Scraper is disabled"},
   *       ),
   *       @OA\Examples(
   *         summary="Failed Request",
   *         example="FailedRequest",
   *         value={"status": 500, "message": "Failed"},
   *       ),
   *     },
   *     @OA\Property(property="status", type="integer", format="int32"),
   *     @OA\Property(property="message", type="string"),
   *   ),
   * )
   */
  private function isScraperEnabled() {
    if (!config('app.scraper.disabled')) {
      if (config('app.scraper.release_base_uri')) {
        return true;
      } else {
        return ErrorResponse::failed('Release URL configuration not found');
      }
    } else {
      ErrorResponse::failed('Release URL / Web Scraper is disabled');
    }
  }

  /**
   * @OA\Schema(
   *   schema="ChangelogItem",
   *   description="Changelog Item",
   *   example={{
   *     "date": "Jan 01, 2020 00:00",
   *     "email": "sample@mail.com",
   *     "name": "Owner",
   *     "message": "updated sub-dependencies",
   *     "module": "module-name",
   *     "url": "{{ GitHub commit URL }}",
   *   }},
   *   type="array",
   *   @OA\Items(
   *     @OA\Property(property="date", type="string"),
   *     @OA\Property(property="email", type="string", format="email"),
   *     @OA\Property(property="name", type="string"),
   *     @OA\Property(property="message", type="string"),
   *     @OA\Property(property="module", type="string"),
   *     @OA\Property(property="url", type="string", format="uri"),
   *   ),
   * )
   */
  private function parseChangelog($data) {
    $changelog = json_decode($data);
    $data = [];

    foreach ($changelog as $change) {
      $rawDate = new DateTime($change->commit->author->date);
      $parsedMessage = $this->parseChangelogMessage($change->commit->message);

      $data[] = [
        'date' => $rawDate->format('M d, Y H:m'),
        'email' => $change->commit->author->email,
        'name' => $change->commit->author->name,
        'message' => $parsedMessage->message,
        'module' => $parsedMessage->module,
        'url' => $change->url,
      ];
    }

    return json_encode($this->categorizeChangelog($data));
  }

  private function categorizeChangelog($changelog) {
    $data = [];
    $keywords = [
      'dep' => ['dependency', 'dependencies'],
      'fix' => ['fixed', 'removed'],
      'new' => ['added', 'functional', 'migrated'],
    ];

    foreach ($changelog as $change) {
      $change = (object) $change;

      if (strpos($change->message, 'Merge branch') === false) {
        $commitDate = 'changes_' . (new DateTime($change->date))->format('Ymd');
        $title = (new DateTime($change->date))->format('M d, Y');

        if (!isset($data[$commitDate])) {
          $data[$commitDate] = [
            'dep' => [],
            'fix' => [],
            'new' => [],
            'improve' => [],
            'title' => $title,
          ];
        }

        $isDep = $this->parseMessageType($change->message, $keywords['dep'])
          && $change->module === '';
        $isFix = $this->parseMessageType($change->message, $keywords['fix']);
        $isNew = $this->parseMessageType($change->message, $keywords['new']);

        if ($isDep) {
          $data[$commitDate]['dep'][] = $change;
        } else if ($isFix) {
          $data[$commitDate]['fix'][] = $change;
        } else if ($isNew) {
          $data[$commitDate]['new'][] = $change;
        } else {
          $data[$commitDate]['improve'][] = $change;
        }
      }
    }

    return $data;
  }

  private function parseChangelogMessage($message) {
    $rawMessage = explode(':', $message);

    if (count($rawMessage) == 1) {
      $rawMessage = preg_split("/ (.+)/", $message);
    }

    if (strpos($rawMessage[1], ', resolved #') !== false) {
      $rawMessage[1] = str_replace(', resolved #', '', $rawMessage[1]);
    }

    $rawModule = ltrim(strtolower($rawMessage[0]));
    $rawModule = str_replace('_', ' ', $rawModule);

    $module = ($rawModule === 'anidb' || $rawModule === 'transition') ? '' : $rawModule;
    $message = ltrim($rawMessage[1]);

    return (object) [
      'module' => $module,
      'message' => $message,
    ];
  }

  private function parseMessageType($message, $keywords) {
    $value = false;

    foreach ($keywords as $key) {
      if (strpos($message, $key) !== false) {
        $value = true;
      }
    }

    return $value;
  }

  private function parseIssues($issues) {
    $issues = json_decode($issues);
    $data = [];

    foreach ($issues as $issue) {
      if ($issue->state === 'open') {
        $labels = [];

        foreach ($issue->labels as $label) {
          if (!($label->name === 'todo' || $label->name === 'in progress')) {
            $className = str_replace(':', '', $label->name);
            $className = strtolower(str_replace(' ', '-', $className));
            $labelName = strtoupper(explode(' ', $label->name)[1]);

            $labels[] = [
              'class' => $className,
              'name' => $labelName,
            ];
          }
        }

        $labels = array_reverse($labels);
        $data[] = [
          'date' => (new DateTime($issue->created_at))->format('M d, Y'),
          'labels' => $labels,
          'number' => $issue->number,
          'title' => $issue->title,
          'url' => $issue->html_url,
        ];
      }
    }

    return $data;
  }
}
