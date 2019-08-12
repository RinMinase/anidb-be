<?php

namespace App\Controllers;

use MongoDB\BSON\ObjectID as MongoID;

class SummerController {

	public function retrieve($params = null) {
		if (is_null($params)) {
			$data = app('mongo')->summer->find();
		} else {
			$data = app('mongo')->summer->find([
				'_id' => new MongoID($params),
			]);
		}

		return response(mongo_json($data))->header('Content-Type', 'application/json');
	}

}
