<?php

namespace App\Controllers;

use DateTime;
use Exception;
use GuzzleHttp\Client;

class ImageController {

	public function retrieve($param) {
		$data = app('firebase')
			->getBucket()
			->object(urldecode($param))
			->signedUrl(new DateTime('tomorrow'), [ 'version' => 'v4' ]);

		return response($this->verifyImageContents($data));
	}

	private function verifyImageContents($url) {
		try {
			$response = (new Client())->get($url)->getHeaderLine('content-type');
			$data = ($response == 'image/jpeg') ? $url : 'Image path is invalid';
		} catch (Exception $e) {
			$data = 'Image path is invalid';
		}

		return $data;
	}

}
