<?php

namespace App\Controllers;

class HomeController {

	// public function query() {
	// 	$data = app('firebase')->getDatabase()->getReference('hdd')->getValue();

	// 	return response()->json($data);
	// }

	public function mongo() {
		$data = app('mongo')->hdd->find();

		return response(mongo_json($data))->header('Content-Type', 'application/json');
	}
}
