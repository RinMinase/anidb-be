<?php

namespace App\Models;

class Anime {

	private $url;
	private $title;
	private $synonyms;
	private $episodes;
	private $premiered;

	public function get() {
		return [
			'url' => $this->url,
			'title' => $this->title,
			'synonyms' => $this->synonyms,
			'episodes' => $this->episodes,
			'premiered' => $this->premiered,
		];
	}

	public function getUrl(): string {
		return $this->url;
	}

	public function getTitle(): string {
		return $this->title;
	}

	public function getSynonyms(): string {
		return $this->synonyms;
	}

	public function getEpisodes(): int {
		return $this->episodes;
	}

	public function getPremiered(): string {
		return $this->premiered;
	}

	public static function parse($input): Anime {
		$instance = new self();

		$instance->url = $input->filterXPath('//meta[@property=\'og:url\']')->attr('content');
		$instance->title = $input->filterXPath('//meta[@property=\'og:title\']')->attr('content');

		$instance->synonyms = $instance->parseSynonyms($input);
		$instance->episodes = $instance->parseEpisodes($input);
		$instance->premiered = $instance->parsePremiered($input);

		return $instance;
	}

	private function parseSynonyms($input): string {
		$title = $input->filterXPath('//span[text()="Synonyms:"]');
		if (!$title->count()) { return null; }

		$titles = trim_dom_crawler($title);

		return $titles;
	}

	private function parseEpisodes($input): int {
		$episodes = $input->filterXPath('//span[text()="Episodes:"]');
		if (!$episodes->count()) { return null; }

		$episodes = trim_dom_crawler($episodes);
		if ($episodes === 'Unknown') { return null; }

		return (int)$episodes;
	}

	private function parsePremiered($input): string {
		$premiered = $input->filterXPath('//span[text()="Premiered:"]');
		if (!$premiered->count()) { return null; }

		$premiered = trim_dom_crawler($premiered);
		if ($premiered === '?') { return null; }

		return $premiered;
	}

}
