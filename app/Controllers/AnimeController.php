<?php

namespace App\Controllers;

use MongoDB\BSON\ObjectID as MongoID;

class AnimeController {

	public function retrieve($params = null) {
		if (is_null($params)) {
			$data = app('mongo')->anime->find();
		} else {
			$data = app('mongo')->anime->find([
				'_id' => new MongoID($params),
			]);
		}

		return response(mongo_json($data))->header('Content-Type', 'application/json');
	}

}
