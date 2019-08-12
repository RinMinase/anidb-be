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

		$data = $this->parseChangelog($data);

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

		$data = $this->parseIssues($data);

		return response($data)->header('Content-Type', 'application/json');
	}

	private function parseChangelog($data) {
		return $data;
	}

	private function parseIssues($data) {
		return $data;
	}

}
