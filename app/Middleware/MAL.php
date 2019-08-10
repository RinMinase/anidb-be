<?php

namespace App\Middleware;

use Exception;
use App\Models\Anime;
use App\Models\AnimeSearch;

class MAL {

	public function anime($id) {
		try {
			return Anime::parse(app('goutte')->request('get', '/anime/' . $id));
		} catch (Exception $e) { throw new Exception('Issues in connecting to MAL Servers'); }
	}

	public function search($query) {
		try {
			return AnimeSearch::parse(app('goutte')->request('get', '/anime.php?q=' . urldecode($query)));
		} catch (Exception $e) { throw new Exception('Issues in connecting to MAL Servers'); }
	}

}
