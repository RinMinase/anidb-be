<?php

namespace App\Middleware;

use Exception;
use App\Models\Anime;
use App\Models\AnimeSearch;

class MAL {

	public function anime($id) {
		try {
			return Anime::parse(app('goutte')
				->request('GET', 'https://' . env('SCRAPER_BASE_URI') . '/anime/' . $id));
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
			// Commented as Base URI is not currently working properly with Goutte
			// https://github.com/FriendsOfPHP/Goutte/issues/427

			return AnimeSearch::parse(app('goutte')
				->request('GET', 'https://' . env('SCRAPER_BASE_URI') . '/anime.php?q=' . urldecode($query)));
			// return AnimeSearch::parse(app('goutte')->request('GET', '/anime.php?q=' . urldecode($query)));
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
