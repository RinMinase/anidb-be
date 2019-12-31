<?php

namespace App\Controllers;

use DateTime;

class ImageController {

	public function retrieve($param) {
		$data = app('firebase')
			->getBucket()
			->object(urldecode($param))
			->signedUrl(new DateTime('tomorrow'), [ 'version' => 'v4' ]);

		return response($data);
	}

}
