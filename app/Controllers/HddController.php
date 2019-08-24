<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use MongoDB\BSON\ObjectID as MongoID;

class HddController {

	public function create(Request $request) {
		if ($request->input('from')
			&& $request->input('to')) {

			app('mongo')->hdd->insertOne([
				'from' => $request->input('from'),
				'to' => $request->input('to'),
				'size' => ($request->input('size')) ? $request->input('size') : 1000169533440,
			]);

			return response('Success');
		} else {
			return response('"from" and "to" fields are required')->setStatusCode(400);
		}
	}

	public function retrieve($params = null, Request $request) {
		if (is_null($params)) {
			$data = app('mongo')->hdd->find(
				[],
				[ 'sort' =>
					[ 'from' => 1 ]
				]
			);
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
		if ($request->input('from')
			|| $request->input('to')
			|| $request->input('size')) {

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

			$query = app('mongo')->hdd->updateOne([
				[ '_id' => new MongoID($params) ],
				[ '$set' => $data ],
			]);

			if ($query->getModifiedCount()) {
				return response('Success');
			} else {
				return response('Failed')->setStatusCode(500);
			}
		} else {
			return response('"from", "to" or "size" fields are required')->setStatusCode(400);
		}
	}

}
