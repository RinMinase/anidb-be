<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use MongoDB\BSON\ObjectID as MongoID;

class AnimeController {

	public function create(Request $request) {
		if ($request->input('title')) {
			$query = app('mongo')->anime->insertOne([
				'dateFinished' => (int) $request->input('dateFinished'),
				'duration' => (int) $request->input('duration'),
				'encoder' => $request->input('encoder'),
				'episodes' => (int) $request->input('episodes'),
				'filesize' => (int) $request->input('filesize'),
				'firstSeasonTitle' => $request->input('firstSeasonTitle'),
				'inhdd' => (bool) $request->input('inhdd'),
				'offquel' => $request->input('offquel'),
				'ovas' => (int) $request->input('ovas'),
				'prequel' => $request->input('prequel'),
				'quality' => $request->input('quality'),
				'rating' => [
					'audio' => 0,
					'enjoyment' => 0,
					'graphics' => 0,
					'plot' => 0,
				],
				'releaseSeason' => $request->input('releaseSeason'),
				'releaseYear' => $request->input('releaseYear'),
				'remarks' => $request->input('remarks'),
				'rewatch' => $request->input('rewatch'),
				'rewatchLast' => (int) $request->input('rewatchLast'),
				'seasonNumber' => (int) $request->input('seasonNumber'),
				'sequel' => $request->input('sequel'),
				'specials' => (int) $request->input('specials'),
				'title' => $request->input('title'),
				'variants' => $request->input('variants'),
				'watchStatus' => (int) $request->input('watchStatus'),
			]);

			return response('Success');
		} else {
			return response('"title" field is required')->setStatusCode(400);
		}
	}

	public function retrieve($params = null, Request $request) {
		if (is_null($params)) {
			if ($request->query('order')) {
				$data = $this->retrieveSpecific(
					$this->parseOrder($request->query('order')),
					$request->query('limit'),
				);
			} else if ($request->query('group') === 'hdd') {
				$data = $this->retrieveByHdd();
			} else if ($request->query('group') === 'release') {
				$data = $this->retrieveByRelease();
			} else if ($request->query('group') === 'title') {
				$data = $this->retrieveByTitle();
			} else {
				$data = $this->retrieveAll();
			}
		} else {
			$data = $this->retrieveAnime($params);
		}

		return response(mongo_json($data))->header('Content-Type', 'application/json');
	}

	private function parseOrder($order) {
		$orders = [];

		if (!!$order) {
			$splitOrders = explode(',', rtrim($order, ','));

			foreach ($splitOrders as $item) {
				$splitItem = explode(':', $item);
				$orders[$splitItem[0]] = ($splitItem[1] === 'desc') ? -1 : 1;
			}
		}

		return $orders;
	}

	private function retrieveAll() {
		return app('mongo')->anime->find();
	}

	private function retrieveAnime($id) {
		return app('mongo')->anime->findOne([ '_id' => new MongoID($id) ]);
	}

	private function retrieveByHdd() {
		return;
	}

	private function retrieveByRelease() {
		return;
	}

	private function retrieveByTitle() {
		return;
	}

	private function retrieveSpecific($orders, $limit) {
		$limit = (is_numeric($limit)) ? (int) $limit : 20;

		return app('mongo')->anime->find([], [
			'limit' => $limit,
			'sort' => $orders,
		]);
	}

}
