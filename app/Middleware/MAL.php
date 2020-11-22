<?php

namespace App\Middleware;

use Exception;
use App\Models\Anime;
use App\Models\AnimeSearch;
use Symfony\Component\DomCrawler\Crawler;

class MAL {

	public function anime($id) {
		try {
			$html = app('scraper')
				->request('GET', '/anime/' . $id)
				->getContent();

			return Anime::parse(new Crawler($html));
		} catch (Exception $e) {
			if (env('APP_DEBUG')) {
				throw new Exception('Issues in connecting to MAL Servers');
			} else {
				return response([
					'status' => 503,
					'message' => 'Issues in connecting to MAL Servers',
				], 503);
			}
		}
	}

	public function search($query) {
		try {
			$html = app('scraper')
				->request('GET', 'anime.php?q=' . urldecode($query))
				->getContent();

			return AnimeSearch::parse(new Crawler($html));
		} catch (Exception $e) {
			if (env('APP_DEBUG')) {
				throw new Exception('Issues in connecting to MAL Servers');
			} else {
				return response([
					'status' => 503,
					'message' => 'Issues in connecting to MAL Servers',
				], 503);
			}
		}
	}

}
