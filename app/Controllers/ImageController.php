<?php

namespace App\Controllers;

use DateTime;
use Exception;
use GuzzleHttp\Client;

class ImageController {

	public function retrieve($param) {
		$url = app('firebase')
			->getBucket()
			->object(urldecode($param))
			->signedUrl(new DateTime('tomorrow'), [ 'version' => 'v4' ]);

		$data = $this->verifyImageContents($url);

		return response()->json($data);
	}

	private function verifyImageContents($url) {
		$invalidMsg = [
			'Status' => 'Invalid',
			'Message' => 'Image path is invalid',
		];

		try {
			$response = (new Client())->get($url)->getHeaderLine('content-type');
			$data = ($response == 'image/jpeg') ? ['url' => $url] : $invalidMsg;
		} catch (Exception $e) {
			$data = $invalidMsg;
		}

		return $data;
	}

}
