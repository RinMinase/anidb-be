<?php

namespace App\Middleware;

use App\Models\Anime;

class MAL {

	public function anime($id) {
		return Anime::parse(app('mal')->request('get', '/anime/' . $id));
	}

}
