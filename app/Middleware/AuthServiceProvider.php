<?php

namespace App\Middleware;

// use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider {

	public function register() {

	}

	public function boot() {
		$this->app['auth']->viaRequest('api', function ($request) {
			if ($request->header('api-key') === env('API_KEY')) {
				return true;
			}
		});
	}
}
