<?php

namespace App\Controllers;

use Illuminate\Http\JsonResponse;
use App\Repositories\LogRepository;

class LogController extends Controller {

  private LogRepository $logRepository;

  public function __construct(LogRepository $logRepository) {
    $this->logRepository = $logRepository;
  }

  /**
   * @api {get} /api/logs Retrieve all logs
   * @apiName LogRetrieve
   * @apiGroup Logs
   *
   * @apiHeader {String} Authorization Token received from logging-in
   *
   * @apiSuccess {Object[]} data Log Data
   * @apiSuccess {UUID} data.id ID of the log
   * @apiSuccess {String} data.table_changed Changed table name of the log
   * @apiSuccess {String} data.id_changed Changed id under the table name
   * @apiSuccess {String} data.description Any description for the table_changed and id_changed
   * @apiSuccess {Number} data.action Action done (add/edit/delete)
   * @apiSuccess {DateTime} data.created_at Creation date of the log
   *
   * @apiSuccessExample Success Response
   *     HTTP/1.1 200 OK
   *     {
   *       "data": [
   *         {
   *           id: "9ef81943-78f0-4d1c-a831-a59fb5af339c",
   *           table_changed: "marathon",
   *           id_changed: 1,
   *           description: "title changed from 'old' to 'new'",
   *           action: "add",
   *           created_at: "2020-01-01 00:00:00",
   *         }
   *       ]
   *     }
   *
   * @apiError Unauthorized There is no login token provided, or the login token provided is invalid
   */
  public function index(): JsonResponse {
    return response()->json([
      'data' => $this->logRepository->getAll(),
    ]);
  }
}
