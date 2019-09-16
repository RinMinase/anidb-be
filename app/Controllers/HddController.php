<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use MongoDB\BSON\ObjectID as MongoID;

class HddController {

	public function create(Request $request) {
		if ($request->input('from')
			&& $request->input('to')) {

			if ($request->input('size')
				&& !is_numeric($request->input('size'))) {
				return response('"size" field is invalid', 400);
			}

			app('mongo')->hdd->insertOne([
				'from' => $request->input('from')[0],
				'to' => $request->input('to')[0],
				'size' => ($request->input('size')) ? (int) $request->input('size') : 1000169533440,
			]);

			return response('Success');
		} else {
			return response('"from" and "to" fields are required', 400);
		}
	}

	public function retrieve($params = null, Request $request) {
		if (is_null($params)) {
			$data = app('mongo')->hdd->find(
				[],
				[ 'sort' => [ 'from' => 1 ] ]
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
			return response('Failed', 500);
		}
	}

	public function update($params, Request $request) {
		if ($request->input('from')
			|| $request->input('to')
			|| $request->input('size')) {

			$data = [];

			if ($request->input('from')) {
				$data['from'] = $request->input('from')[0];
			}

			if ($request->input('to')) {
				$data['to'] = $request->input('to')[0];
			}

			if ($request->input('size')) {
				if (!is_numeric($request->input('size'))) {
					return response('"size" field is invalid', 400);
				}

				$data['size'] = (int) $request->input('size');
			}

			$query = app('mongo')->hdd->updateOne(
				[ '_id' => new MongoID($params) ],
				[ '$set' => $data ]
			);

			if ($query->getModifiedCount()) {
				return response('Success');
			} else {
				return response('Failed', 500);
			}
		} else {
			return response('"from", "to" or "size" fields are required', 400);
		}
	}

}
