<?php

namespace App\Controllers;

use DateTime;

class ReleaseController {

	public function changelog($limit = 20) {
		$data = app('release')
			->get('commits', [
				'query' => [
					'per_page' => $limit,
				],
			])
			->getBody()
			->getContents();

		$data = $this->parseChangelog($data);

		return response($data)->header('Content-Type', 'application/json');
	}

	public function issues($limit = 100) {
		$data = app('release')
			->get('issues', [
				'query' => [
					'per_page' => $limit,
					'page' => 1,
				],
			])
			->getBody()
			->getContents();

		$data = $this->parseIssues($data);

		return response($data)->header('Content-Type', 'application/json');
	}

	private function parseChangelog($data) {
		$changelog = json_decode($data);
		$data = [];

		foreach ($changelog as $change) {
			$rawDate = new DateTime($change->commit->author->date);
			$parsedMessage = $this->parseChangelogMessage($change->commit->message);

			$data[] = [
				'date' => $rawDate->format('M d, Y H:m'),
				'email' => $change->commit->author->email,
				'name' => $change->commit->author->name,
				'message' => $parsedMessage->message,
				'module' => $parsedMessage->module,
				'url' => $change->url,
			];
		}

		return json_encode($data);
	}

	private function parseIssues($data) {
		return $data;
	}

	private function parseChangelogMessage($message) {
		$rawMessage = explode(':', $message);

		if (count($rawMessage) == 1) {
			$rawMessage = preg_split("/ (.+)/", $message);
		}

		if (strpos($rawMessage[1], ', resolved #') !== false) {
			$rawMessage[1] = str_replace(', resolved #', '', $rawMessage[1]);
		}

		$rawModule = ltrim(strtolower($rawMessage[0]));
		$rawModule = str_replace('_', ' ', $rawModule);

		$module = ($rawModule === 'anidb' || $rawModule === 'transition') ? '' : $rawModule;
		$message = ltrim($rawMessage[1]);

		return (object) [
			'module' => $module,
			'message' => $message,
		];
	}

}
