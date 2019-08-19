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
			return response('"timeStart", "timeEnd" and "title" fields are required')
			->setStatusCode(400);
		}
	}

	public function remove($params, Request $request) {
		$query = app('mongo')->summer->deleteOne([ '_id' => new MongoID($params) ]);

		if ($query->getDeletedCount()) {
			return response('Success');
		} else {
			return response('Failed')->setStatusCode(500);
		}
	}

	public function retrieve($params = null, Request $request) {
		if (is_null($params)) {
			$data = app('mongo')->summer->find();
		} else {
			$data = app('mongo')->summer->find([
				'_id' => new MongoID($params),
			]);
		}

		return response(mongo_json($data))->header('Content-Type', 'application/json');
	}

	public function update($params, Request $request) {
		$data = [];

		if ($request->input('timeEnd')) {
			$data['timeEnd'] = $request->input('timeEnd');
		}

		if ($request->input('timeStart')) {
			$data['timeStart'] = $request->input('timeStart');
		}

		if ($request->input('title')) {
			$data['title'] = $request->input('title');
		}

		$query = app('mongo')->summer->updateOne(
			[ '_id' => new MongoID($params) ],
			[ '$set' => $data ],
		);

		if ($query->getModifiedCount()) {
			return response('Success');
		} else {
			return response('Failed')->setStatusCode(500);
		}
	}

}
