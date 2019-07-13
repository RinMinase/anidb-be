<?php

namespace App\Models;

class Anime {

	private $url;
	private $title;

	public static function parse($input): Anime {
		$instance = new self();

		$instance->url = $input->filterXPath('//meta[@property=\'og:url\']')->attr('content');
		$instance->title = $input->filterXPath('//meta[@property=\'og:title\']')->attr('content');

		return $instance;
	}

	public function get() {
		return [
			'url' => $this->url,
			'title' => $this->title,
		];
	}

	public function getUrl(): string {
		return $this->url;
	}

	public function getTitle(): string {
		return $this->title;
	}

}
