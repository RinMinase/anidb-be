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

	public function changelogBE($limit = 20) {
		$data = app('release_be')
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

		return json_encode($this->categorizeChangelog($data));
	}

	private function categorizeChangelog($changelog) {
		$data = [];
		$keywords = [
			'dep' => [ 'dependency', 'dependencies' ],
			'fix' => [ 'fixed', 'removed' ],
			'new' => [ 'added', 'functional', 'migrated' ],
		];

		foreach ($changelog as $change) {
			$change = (object) $change;

			if (strpos($change->message, 'Merge branch') === false) {
				$commitDate = 'changes_' . (new DateTime($change->date))->format('Ymd');
				$title = (new DateTime($change->date))->format('M d, Y');

				if (!isset($data[$commitDate])) {
					$data[$commitDate] = [
						'dep' => [],
						'fix' => [],
						'new' => [],
						'improve' => [],
						'title' => $title,
					];
				}

				$isDep = $this->parseMessageType($change->message, $keywords['dep'])
					&& $change->module === '';
				$isFix = $this->parseMessageType($change->message, $keywords['fix']);
				$isNew = $this->parseMessageType($change->message, $keywords['new']);

				if ($isDep) {
					$data[$commitDate]['dep'][] = $change;
				} else if ($isFix) {
					$data[$commitDate]['fix'][] = $change;
				} else if ($isNew) {
					$data[$commitDate]['new'][] = $change;
				} else {
					$data[$commitDate]['improve'][] = $change;
				}

			}
		}

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

	private function parseMessageType($message, $keywords) {
		$value = false;

		foreach($keywords as $key) {
			if (strpos($message, $key) !== false) {
				$value = true;
			}
		}

		return $value;
	}

	private function parseIssues($issues) {
		$issues = json_decode($issues);
		$data = [];

		foreach ($issues as $issue) {
			if ($issue->state === 'open') {
				$labels = [];

				foreach ($issue->labels as $label) {
					if (!($label->name === 'todo' || $label->name === 'in progress')) {
						$className = str_replace(':', '', $label->name);
						$className = strtolower(str_replace(' ', '-', $className));
						$labelName = strtoupper(explode(' ', $label->name)[1]);

						$labels[] = [
							'class' => $className,
							'name' => $labelName,
						];
					}
				}

				$labels = array_reverse($labels);
				$data[] = [
					'date' => (new DateTime($issue->created_at))->format('M d, Y'),
					'labels' => $labels,
					'number' => $issue->number,
					'title' => $issue->title,
					'url' => $issue->html_url,
				];
			}
		}

		return $data;
	}

}
