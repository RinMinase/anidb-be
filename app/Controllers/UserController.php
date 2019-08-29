<?php

namespace App\Controllers;

use Illuminate\Http\Request;

class UserController {

	public function register(Request $request) {
		if ($request->header('api-key') === env('API_KEY')) {
			if ($request->input('username') && $request->input('password')) {
				$isExisting = (app('mongo')
					->users
					->findOne(['username' => $request->input('username')]))
					->username;

				if ($isExisting) {
					return response('"username" is already taken')->setStatusCode(400);
				}

				app('mongo')->users->insertOne([
					'username' => $request->input('username'),
					'password' => password_hash($request->input('password'), PASSWORD_ARGON2ID),
					'level' => 2,
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

}
