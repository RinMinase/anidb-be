<?php

namespace App\Controllers;

class MalController {

	public function queryMal($params) {
		if (is_numeric($params)) {
			return $this->getAnime($params);
		} else {
			return $this->searchAnime($params);
		}
	}

	private function getAnime($id = 37430) {
		$data = app('mal')->anime($id)->get();

		return response()->json($data);
	}

	private function searchAnime($query) {
		$data = app('mal')->search($query)->get();

		return response()->json($data);
	}

}
