<?php

namespace App\Controllers;

use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ReleaseController extends Controller {

  protected $feReleaseURI;
  protected $beReleaseURI;

  public function __construct() {
    if ($this->isScraperEnabled()) {
      $this->feReleaseURI = env('RELEASE_BASE_URI') . '/';
      $this->beReleaseURI = env('RELEASE_BASE_URI') . '-be/';
    }
  }

  /**
   * @api {get} /api/changelog/:limit? Front-end Changelog
   * @apiName ChangelogFE
   * @apiGroup Release
   *
   * @apiHeader {String} Authorization Token received from logging-in
   * @apiParam {String} limit Page Limit (Optional)
   *
   * @apiSuccess {Array} dep Dependencies type of changes
   * @apiSuccess {Array} fix Fix type of changes
   * @apiSuccess {Array} new New type of changes
   * @apiSuccess {Array} improve Improvement type of changes
   * @apiSuccess {String} title Date of changelist
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     [
   *       "changes_{{date}}": {
   *         dep: [
   *           {
   *             date: "Jan 01, 2020 00:00"
   *             email: "sample@mail.com"
   *             name: "Owner"
   *             message: "updated sub-dependencies"
   *             module: ""
   *             url: "{{ GitHub commit URL }}"
   *           }
   *         ],
   *         fix: [],
   *         new: [],
   *         improve: [],
   *         title: "Jan 01, 2020",
   *       },
   *       "changes_{{date}}": { ... },
   *     ]
   *
   * @apiError Unauthorized There is no api-key provided, or the api-key provided is invalid
   */
  public function getLogs($limit = 20): JsonResponse {
    if ($this->isScraperEnabled()) {
      $data = Http::get($this->feReleaseURI . 'commits', [
        'query' => [
          'per_page' => $limit,
        ],
      ])->body();

      $data = $this->parseChangelog($data);

      return response($data)
        ->header('Content-Type', 'application/json');
    }
  }

  /**
   * @api {get} /api/changelog-be/:limit? Back-end Changelog
   * @apiName ChangelogBE
   * @apiGroup Release
   *
   * @apiHeader {String} Authorization Token received from logging-in
   * @apiParam {String} limit Page Limit (Optional)
   *
   * @apiSuccess {Array} dep Dependencies type of changes
   * @apiSuccess {Array} fix Fix type of changes
   * @apiSuccess {Array} new New type of changes
   * @apiSuccess {Array} improve Improvement type of changes
   * @apiSuccess {String} title Date of changelist
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     [
   *       "changes_{{date}}": {
   *         dep: [
   *           {
   *             date: "Jan 01, 2020 00:00"
   *             email: "sample@mail.com"
   *             name: "Owner"
   *             message: "updated sub-dependencies"
   *             module: ""
   *             url: "{{ GitHub commit URL }}"
   *           }
   *         ],
   *         fix: [],
   *         new: [],
   *         improve: [],
   *         title: "Jan 01, 2020",
   *       },
   *       "changes_{{date}}": { ... },
   *     ]
   *
   * @apiError Unauthorized There is no api-key provided, or the api-key provided is invalid
   */
  public function getLogsBE($limit = 20): JsonResponse {
    if ($this->isScraperEnabled()) {
      $data = Http::get($this->beReleaseURI . 'commits', [
        'query' => [
          'per_page' => $limit,
        ],
      ])->body();

      $data = $this->parseChangelog($data);

      return response($data)
        ->header('Content-Type', 'application/json');
    }
  }

  /**
   * @api {get} /api/issues/:limit? Issues List
   * @apiName Issues
   * @apiGroup Release
   *
   * @apiHeader {String} Authorization Token received from logging-in
   * @apiParam {String} limit Page Limit (Optional)
   *
   * @apiSuccess {String} date Date of posted issue
   * @apiSuccess {Array} labels Issue labels
   * @apiSuccess {Number} number Issue number
   * @apiSuccess {String} title Issue title
   * @apiSuccess {String} url GitHub issue URL
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     [
   *       {
   *         date: "Jan 01, 2020",
   *         labels: [
   *           {
   *             class: "type-enhancement"
   *             name: "ENHANCEMENT"
   *           }, {
   *             class: "type-priority-high"
   *             name: "HIGH"
   *           },
   *         ],
   *         number: 100,
   *         title: "This is a sample issue title",
   *         url: {{ GitHub issue URL }},
   *       }, { ... },
   *     }
   *
   * @apiError Unauthorized There is no api-key provided, or the api-key provided is invalid
   */
  public function getIssues($limit = 100): JsonResponse {
    if ($this->isScraperEnabled()) {
      $data = Http::get($this->beReleaseURI . 'issues', [
        'query' => [
          'per_page' => $limit,
          'page' => 1,
        ],
      ])->body();

      $data = $this->parseIssues($data);

      return response($data)
        ->header('Content-Type', 'application/json');
    }
  }

  private function isScraperEnabled() {
    if (!env('DISABLE_SCRAPER')) {
      if (env('RELEASE_BASE_URI')) {
        return true;
      } else {
        return response()->json([
          'status' => 500,
          'message' => 'Release URL configuration not found',
        ], 500);
      }
    } else {
      return response()->json([
        'status' => 500,
        'message' => 'Release URL / Web Scraper is disabled',
      ], 500);
    }
  }

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
