<?php

namespace App\Middleware;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider {

	public function register() {

	}

	public function boot() {
		$this->app['auth']->viaRequest('api', function ($request) {
			$currentSession = app('mongo')->session->findOne([ 'token' => $request->header('token') ]);
			$isValidToken = $currentSession !== null;

			if ($isValidToken) {
				return true;
			}
		});
	}
}
