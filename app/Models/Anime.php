<?php

namespace App\Models;

class Anime {

	private $url;
	private $title;
	private $synonyms;
	private $episodes;
	private $premiered;

	public static function parse($input): Anime {
		$instance = new self();

		$instance->url = $input->filterXPath('//meta[@property=\'og:url\']')->attr('content');
		$instance->title = $input->filterXPath('//meta[@property=\'og:title\']')->attr('content');

		$instance->synonyms = $instance->parseSynonyms($input);
		$instance->episodes = $instance->parseEpisodes($input);
		$instance->premiered = $instance->parsePremiered($input);

		return $instance;
	}

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

	private function parseSynonyms($input): string {
		$title = $input->filterXPath('//span[text()="Synonyms:"]');
		if (!$title->count()) { return null; }

		$titles = trim(str_replace($title->text(), '', $title->parents()->text()));

		return $titles;
	}

	private function parseEpisodes($input): int {
		$episodes = $input->filterXPath('//span[text()="Episodes:"]');
		if (!$episodes->count()) { return null; }

		$episodes = trim(str_replace($episodes->text(), '', $episodes->parents()->text()));
		if ($episodes === 'Unknown') { return null; }

		return (int)$episodes;
	}

	private function parsePremiered($input): string {
		$premiered = $input->filterXPath('//span[text()="Premiered:"]');
		if (!$premiered->count()) { return null; }

		$premiered = trim(str_replace($premiered->text(), '', $premiered->parents()->text()));
		if ($premiered === '?') { return null; }

		return $premiered;
	}

}
