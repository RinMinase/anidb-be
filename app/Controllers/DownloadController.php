<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use MongoDB\BSON\ObjectID as MongoID;

class DownloadController {

	public function retrieve($params = null, Request $request) {
		if (is_authenticated($request)) {
			if (is_null($params)) {
				$data = app('mongo')->download->find();
			} else {
				$data = app('mongo')->download->find([
					'_id' => new MongoID($params),
				]);
			}

			return response(mongo_json($data))->header('Content-Type', 'application/json');
		}

		return response('API Key Required')->setStatusCode(401);
	}

}
