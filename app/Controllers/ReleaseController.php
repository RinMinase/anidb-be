<?php

namespace App\Controllers;

class ReleaseController {

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

	public function issues($limit = 100) {
		$data = app('release')
			->get('issues', [
				'query' => [
					'per_page' => $limit,
					'page' => 1,
				],
			])
			->getBody()
			->getContents();

		return response($data)->header('Content-Type', 'application/json');
	}

}
