<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use MongoDB\BSON\ObjectID as MongoID;

class HddController {

	public function retrieve($params = null, Request $request) {
		if (is_null($params)) {
			$data = app('mongo')->hdd->find();
		} else {
			$data = app('mongo')->hdd->find([ '_id' => new MongoID($params) ]);
		}

		return response(mongo_json($data))->header('Content-Type', 'application/json');
	}

	public function remove($params, Request $request) {
		$query = app('mongo')->hdd->deleteOne([ '_id' => new MongoID($params) ]);

		if ($query->getDeletedCount()) {
			return response('Success');
		} else {
			return response('Failed')->setStatusCode(500);
		}
	}

	public function update($params, Request $request) {
		$data = [];

		if ($request->input('from')) {
			$data['from'] = $request->input('from');
		}

		if ($request->input('to')) {
			$data['to'] = $request->input('to');
		}

		if ($request->input('size')) {
			$data['size'] = $request->input('size');
		}

		if ($request->input('number')) {
			$data['number'] = $request->input('number');
		}

		$query = app('mongo')->hdd->updateOne(
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
