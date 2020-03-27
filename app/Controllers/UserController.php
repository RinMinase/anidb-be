<?php

namespace App\Controllers;

use DateTime;
use DateInterval;
use Illuminate\Http\Request;

class UserController {

	public function login(Request $request) {
		if ($request->header('api-key') !== env('API_KEY')) {
			return response([
				'status' => 401,
				'message' => 'Unauthorized',
			], 401);
		}

		if (!$request->input('username') || !$request->input('password')) {
			return response([
				'status' => 400,
				'message' => '"username" and "password" fields are required',
			], 400);
		}

		$user = app('mongo')->users->findOne(['username' => $request->input('username')]);

		if (!isset($user)) {
			return response([
				'status' => 400,
				'message' => '"username" or "password" is invalid',
			], 400);
		}

		$isVerified = password_verify($request->input('password'), $user->password);

		if (!$isVerified) {
			return response([
				'status' => 400,
				'message' => '"username" or "password" is invalid',
			], 400);
		}

		$this->checkIfUserIsLoggedIn($user);

		$data = $this->generateLoginToken($user);
		app('mongo')->session->insertOne($data);
		app('mongo')->users->updateOne(
			[ 'username' => $request->input('username') ],
			[ '$set' => [ 'last_login' => (new DateTime())->getTimestamp() ] ],
		);

		return response()->json($data);
	}

	public function logout(Request $request) {
		if ($request->header('api-key') !== env('API_KEY')) {
			return response('Unauthorized', 401);
		}

		if (!$request->input('token')) {
			return response([
				'status' => 400,
				'message' => '"token" is required',
			], 400);
		}

		$query = app('mongo')->session->deleteOne([ 'token' => $request->input('token') ]);

		if ($query->getDeletedCount()) {
			return response([
				'status' => 200,
				'message' => 'Success',
			], 200);
		} else {
			return response([
				'status' => 400,
				'message' => 'Session token not found',
			], 400);
		}
	}

	public function register(Request $request) {
		if ($request->header('api-key') !== env('API_KEY')) {
			return response('Unauthorized', 401);
		}

		if (!$request->input('username') || !$request->input('password')) {
			return response('"username" and "password" fields are required', 400);
		}

		$isExisting = app('mongo')->users->findOne([ 'username' => $request->input('username') ]);

		if (isset($isExisting->username)) {
			return response('"username" is already taken', 400);
		}

		app('mongo')->users->insertOne([
			'username' => $request->input('username'),
			'password' => password_hash($request->input('password'), PASSWORD_BCRYPT),
			'role' => 3,
			'last_login' => null,
			'date_created' => (new DateTime())->getTimestamp(),
		]);

		return response('Success');
	}

	private function checkIfUserIsLoggedIn($user) {
		$userLoggedIn = app('mongo')->session->findOne(
			[ 'username' => $user->username ],
			[ 'sort' => [ 'timeout' => -1 ] ],
		);

		if (isset($userLoggedIn)) {
			$isLoggedIn = (new DateTime())->getTimestamp() <= $userLoggedIn->timeout;

			if ($isLoggedIn) {
				return response()->json([
					'username' => $userLoggedIn->username,
					'role' => $userLoggedIn->role,
					'token' => $userLoggedIn->token,
					'timeout' => $userLoggedIn->timeout,
				]);
			}
		}
	}

	private function generateLoginToken($user) {
		if ($user->role === 3 || $user->role === 2) {
			$timeout = (new DateTime())->add(new DateInterval('PT3H'))->getTimestamp();
		} else if ($user->role === 1) {
			$timeout = (new DateTime())->add(new DateInterval('P7D'))->getTimestamp();
		}

		return [
			'username' => $user->username,
			'role' => $user->role,
			'token' => random_string(64),
			'timeout' => $timeout,
		];
	}

}
