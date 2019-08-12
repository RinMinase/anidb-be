<?php

namespace App\Controllers;

class HomeController {

	public function changelog($limit = 20) {
		$data = app('release')
			->get('commits', [
				'query' => [
					'per_page' => $limit,
				],
			])
			->getBody()
			->getContents();

		return response($data)->header('Content-Type', 'application/json');
	}

	public function mongo() {
		$data = app('mongo')->hdd->find();

		return response(mongo_json($data))->header('Content-Type', 'application/json');
	}
}
