<?php

namespace App\Controllers;

use DateTime;

class HomeController {

	public function query($param = null) {
		$data = app('firebase')
			->getStorage()
			->getBucket()
			->object('assets/user.jpg')
			->signedUrl(new DateTime('tomorrow'));

		return response($data);
	}

	public function mongo() {
		$data = app('mongo')->hdd->find();

		return response(mongo_json($data))->header('Content-Type', 'application/json');
	}
}
