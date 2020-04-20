<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization, api-key, token');
set_time_limit(0);

require_once __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(dirname(__DIR__)))->bootstrap();


/* Set the default timezone */

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));


/* Create The Application */

$app = new Laravel\Lumen\Application(dirname(__DIR__));
// $app->withFacades();
// $app->withEloquent();


/* Register Error Handler */

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();


/* Register Container Bindings */

$app->singleton(
	Illuminate\Contracts\Console\Kernel::class,
	App\Commands\Kernel::class
);


/* Register Middleware and Providers */

$app->routeMiddleware([ 'auth' => App\Middleware\Authenticate::class ]);
$app->register(App\Middleware\AuthServiceProvider::class);

if (!env('DISABLE_SCRAPER')) {
	$app->mal = new App\Middleware\MAL();
}


/* Load The Application Routes */

$app->router->group([
	'namespace' => 'App\Controllers',
], function ($router) { require __DIR__.'/routes.php'; });


/* Register Goute and Guzzle */

if (!env('DISABLE_SCRAPER')) {
	if (env('SCRAPER_BASE_URI')) {
		$guzzleClient = new GuzzleHttp\Client([
			'base_uri' => 'https://' . env('SCRAPER_BASE_URI'),
			'timeout' => 10,
		]);

		$app->goutte = (new Goutte\Client())->setClient($guzzleClient);
	} else {
		throw new Exception('Web Scraper configuration not found');
	}

	if (env('RELEASE_BASE_URI')) {
		$app->release = new GuzzleHttp\Client([
			'base_uri' => 'https://' . env('RELEASE_BASE_URI') . '/',
			'timeout' => 10,
		]);

		$app->release_be = new GuzzleHttp\Client([
			'base_uri' => 'https://' . env('RELEASE_BASE_URI') . '-be/',
			'timeout' => 10,
		]);
	} else {
		throw new Exception('Release URL configuration not found');
	}
}


/* Register Firebase DB */

if (!env('DISABLE_FIREBASE')) {
	$creds = json_encode([
		'project_id' => env('FIRE_PROJECT_ID', ''),
		'private_key' => env('FIRE_KEY', ''),
		'client_email' => env('FIRE_EMAIL', ''),
		'client_id' => env('FIRE_CLIENT_ID', ''),
	]);

	$validatedCreds = str_replace('\\\\n', '\\n', $creds);

	$app->firebase = (new Kreait\Firebase\Factory)
		-> withServiceAccount(Kreait\Firebase\ServiceAccount::fromJson($validatedCreds))
		-> withDisabledAutoDiscovery()
		-> createStorage();
}


/* Register Mongo DB */

if (!env('DISABLE_DB')) {
	if (env('DB_USERNAME') && env('DB_PASSWORD') && env('DB_CLUSTER') && env('DB_DATABASE')) {
		$mongoURI = 'mongodb+srv://'
			. env('DB_USERNAME', '') . ':'
			. env('DB_PASSWORD', '') . '@'
			. env('DB_CLUSTER', '') . '/'
			. env('DB_DATABASE', '') . '?retryWrites=true&w=majority';

		$app->mongo = (new MongoDB\Client($mongoURI))->anidb;
	} else {
		throw new Exception('MongoDB Atlas configuration not found');
	}
}


/* Register Mailgun */

if (!env('DISABLE_MAILGUN')) {
	if (env('MAILGUN_API_KEY') && env('MAILGUN_DOMAIN')) {
		$app->mail = Mailgun\Mailgun::create(env('MAILGUN_API_KEY'));
	} else {
		throw new Exception('Mailgun configuration not found');
	}
}


/* Return Application Configurations */
return $app;
