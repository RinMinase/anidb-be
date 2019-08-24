<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use MongoDB\BSON\ObjectID as MongoID;

class HddController {

	public function create(Request $request) {
		if ($request->input('from')
			&& $request->input('to')) {

			$hddData = app('mongo')->hdd->find()->toArray();
			$number = $this->parseHddNumber($hddData, $request->input('from'));

			$this->reorderHdd($hddData, $number);

			app('mongo')->hdd->insertOne([
				'from' => $request->input('from'),
				'to' => $request->input('to'),
				'size' => ($request->input('size')) ? $request->input('size') : 1000169533440,
				'number' => $number,
			]);

			return response('Success');
		} else {
			return response('"timeStart", "timeEnd" and "title" fields are required')
			->setStatusCode(400);
		}
	}

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
		// $data = [];
		// $number = $this->parseHddNumber(app('mongo')->hdd->find());

		// if ($request->input('from')) {
		// 	$data['from'] = $request->input('from');
		// }

		// if ($request->input('to')) {
		// 	$data['to'] = $request->input('to');
		// }

		// if ($request->input('size')) {
		// 	$data['size'] = ($request->input('size')) ? $request->input('size') : 1000169533440;
		// }

		// $number = $this->parseHddNumber();
		// $data['number'] = $this->parseHddNumber(app('mongo')->hdd->find(), $params);

		// $query = app('mongo')->hdd->updateOne(
		// 	[ '_id' => new MongoID($params) ],
		// 	[ '$set' => $data ],
		// );

		// if ($query->getModifiedCount()) {
		// 	return response('Success');
		// } else {
		// 	return response('Failed')->setStatusCode(500);
		// }
	}

	private function parseHddNumber($hddData, $from) {
		foreach ($hddData as $hdd) {
			if (ord($hdd->to) > ord($from) && ord($hdd->from) >= ord($from)) {
				return $hdd->number + 1;
			}
		}
	}

	private function reorderHdd($hddData, $number) {
		$data = [];

		foreach ($hddData as $hdd) {
			if ($hdd->number >= $number) {
				app('mongo')->hdd->updateOne(
					[ '_id' => $hdd['_id'] ],
					[ '$set' => [ 'number' => $hdd->number + 1 ] ],
				);
			}
		}
	}

}
