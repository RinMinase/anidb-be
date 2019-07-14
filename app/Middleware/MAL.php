<?php

namespace App\Middleware;

use App\Models\Anime;

class MAL {

	public function anime($id) {
		return Anime::parse(app('goutte')->request('get', '/anime/' . $id));
	}

}
