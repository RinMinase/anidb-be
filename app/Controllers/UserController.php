<?php

namespace App\Controllers;

use DateTime;
use DateInterval;
use Illuminate\Http\Request;

class UserController {

	public function login(Request $request) {
		if ($request->header('api-key') === env('API_KEY')) {
			if ($request->input('username') && $request->input('password')) {
				$user = app('mongo')->users->findOne(['username' => $request->input('username')]);

				if (isset($user)) {
					$isVerified = password_verify($request->input('password'), $user->password);

					if ($isVerified) {
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

						if ($user->role === 3 || $user->role === 2) {
							$timeout = (new DateTime())->add(new DateInterval('PT3H'))->getTimestamp();
						} else if ($user->role === 1) {
							$timeout = (new DateTime())->add(new DateInterval('P7D'))->getTimestamp();
						}

						$data = [
							'username' => $user->username,
							'role' => $user->role,
							'token' => $this->generateRandomString(),
							'timeout' => $timeout,
						];

						app('mongo')->session->insertOne($data);

						return response()->json($data);
					} else {
						return response('"username" or "password" is invalid')->setStatusCode(403);
					}
				} else {
					return response('"username" does not exist')->setStatusCode(403);
				}
			} else {
				return response('"username" and "password" fields are required')->setStatusCode(400);
			}
		} else {
			return response('Unauthorized')->setStatusCode(401);
		}
	}

	public function register(Request $request) {
		if ($request->header('api-key') === env('API_KEY')) {
			if ($request->input('username') && $request->input('password')) {
				$isExisting = app('mongo')->users->findOne(
					[ 'username' => $request->input('username') ],
				);

				if (isset($isExisting->username)) {
					return response('"username" is already taken')->setStatusCode(400);
				}

				app('mongo')->users->insertOne([
					'username' => $request->input('username'),
					'password' => password_hash($request->input('password'), PASSWORD_BCRYPT),
					'role' => 3,
					'last_login' => null,
				]);

				return response('Success');
			} else {
				return response('"username" and "password" fields are required')
					->setStatusCode(400);
			}
		} else {
			return response('Unauthorized')->setStatusCode(401);
		}
	}

	private function generateRandomString($length = 64) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';

		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}

		return $randomString;
	}

}
