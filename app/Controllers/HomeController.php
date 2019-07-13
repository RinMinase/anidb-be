<?php

namespace App\Controllers;

use App\Middleware\MAL;

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

	public function mal() {
		$mal = new MAL();

		dd($mal->anime(37430));

		// return response($data)->header('Content-Type', 'application/json');
	}

	public function query() {
		$data = app('firebase')->getDatabase()->getReference('hdd')->getValue();

		return response()->json($data);
	}

	public function mongo() {
		$data = app('mongo')->hdd->find();

		return response(mongo_json($data))->header('Content-Type', 'application/json');
	}
}
