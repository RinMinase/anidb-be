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
			$client = new Client();
			$response = $client->request('GET', $url)->getHeaderLine('content-type');
			$data = ($response == 'image/jpeg') ? $url : 'error';
		} catch (Exception $e) {
			$data = 'Image path is invalid';
		}

		return $data;
	}

}
