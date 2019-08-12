<?php

namespace App\Controllers;

class HomeController {

	public function index() {
		$data = [
			'status' => '200',
			'details' => [
				'id' => 1,
				'email' => 'test@email.com',
				'mobile' => '123000123',
				'message' => 'This is a test response'
			]
		];

		return response()->json($data);
	}

	public function mal($id = 37430) {
		$data = app('mal')->anime($id)->get();

		return response()->json($data);
	}

	public function mal_search($query = 'kimi no') {
		$data = app('mal')->search($query)->get();

		return response()->json($data);
	}

	// public function query() {
	// 	$data = app('firebase')->getDatabase()->getReference('hdd')->getValue();

	// 	return response()->json($data);
	// }

	public function mongo() {
		$data = app('mongo')->hdd->find();

		return response(mongo_json($data))->header('Content-Type', 'application/json');
	}
}
