<?php

namespace App\Middleware;

class MAL {

	public function anime($id) {
		return app('mal')->request('get', '/anime/' . $id);
	}

}
