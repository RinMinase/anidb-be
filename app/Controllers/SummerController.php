<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use MongoDB\BSON\ObjectID as MongoID;

class SummerController {

	public function create(Request $request) {
		if ($request->input('timeStart')
			&& $request->input('timeEnd')
			&& $request->input('title')) {

			$query = app('mongo')->summer->insertOne([
				'timeStart' => $request->input('timeStart'),
				'timeEnd' => $request->input('timeEnd'),
				'title' => $request->input('title'),
			]);

			return response('Success');
		} else {
			return response('"timeStart", "timeEnd" and "title" fields are required')->setStatusCode(400);
		}
	}

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
