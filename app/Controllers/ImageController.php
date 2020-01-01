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
		$statusCode = (array_key_exists('Status', $data)) ? 400 : 200;

		return response()->json($data, $statusCode);
	}

	private function verifyImageContents($url) {
		$invalidMsg = [
			'status' => 'Invalid',
			'message' => 'Image path is invalid',
		];

		try {
			$type = (new Client())->get($url)->getHeaderLine('content-type');
			$data = (strpos($type, 'image') !== false) ? ['url' => $url] : $invalidMsg;
		} catch (Exception $e) {
			$data = $invalidMsg;
		}

		return $data;
	}

}
