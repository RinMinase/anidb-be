<?php

namespace App\Controllers;

use DateTime;

class ImageController {

	public function retrieve($param) {
		$data = app('firebase')
			->getBucket()
			->object(urldecode($param))
			->signedUrl(new DateTime('tomorrow'));

		return response($data);
	}

}
