<?php

namespace App\Controllers;

class HomeController {

	public function mongo() {
		$data = app('mongo')->hdd->find();

		return response(mongo_json($data))->header('Content-Type', 'application/json');
	}
}
