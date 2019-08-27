<?php

namespace App\Controllers;

class LogsController {

	public function create($url, $action) {
		if ($url && $action) {
			$query = app('mongo')->logs->insertOne([
				'timestamp' => (new DateTime())->getTimestamp(),
				'url' => $url,
				'action' => $action,
			]);
		} else {
			return response('Required logs were not generated')->setStatusCode(500);
		}
	}

	public function retrieve() {
		$data = app('mongo')->logs->find();

		return response(mongo_json($data))->header('Content-Type', 'application/json');
	}

}
