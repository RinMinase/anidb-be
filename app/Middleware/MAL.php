<?php

namespace App\Middleware;

use App\Models\Anime;
use App\Models\AnimeSearch;

class MAL {

	public function anime($id) {
		return Anime::parse(app('goutte')->request('get', '/anime/' . $id));
	}

	public function search($query) {
		return AnimeSearch::parse(app('goutte')->request('get', '/anime.php?q=' . urlencode($query)));
	}

}
