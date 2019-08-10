<?php

require_once __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(dirname(__DIR__)))->bootstrap();

/* Create The Application */

$app = new Laravel\Lumen\Application(dirname(__DIR__));
// $app->withFacades();
// $app->withEloquent();


/* Register Container Bindings */

$app->singleton(Illuminate\Contracts\Console\Kernel::class, Laravel\Lumen\Console\Kernel::class);


/* Register Middleware */

// $app->routeMiddleware([
//     'auth' => App\Middleware\Authenticate::class,
// ]);

use App\Middleware\MAL;

$app->mal = new MAL();


/* Load The Application Routes */

$app->router->group([
	'namespace' => 'App\Controllers',
], function ($router) { require __DIR__.'/../app/Routes/web.php'; });


/* Register Goute and Guzzle */

use Goutte\Client as GoutteClient;
use GuzzleHttp\Client as GuzzleClient;

if (env('SCRAPER_BASE_URI')) {
	$guzzleClient = new GuzzleClient([
		'base_uri' => 'https://' . env('SCRAPER_BASE_URI'),
		'timeout' => 10,
	]);

	$app->goutte = (new GoutteClient())->setClient($guzzleClient);
} else {
	throw new Exception('Web Scraper configuration not found');
}


/* Register Firebase DB */

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

$creds = json_encode([
	'type' => 'service_account',
	'project_id' => env('FIRE_PROJECT_ID', ''),
	'private_key_id' => env('FIRE_PRIVATE_KEY', ''),
	'private_key' => env('FIRE_KEY', ''),
	'client_email' => env('FIRE_EMAIL', ''),
	'client_id' => env('FIRE_CLIENT_ID', ''),
	'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
	'token_uri' => 'https://oauth2.googleapis.com/token',
	'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
	'client_x509_cert_url' => env('FIRE_CERT_URL', ''),
]);

$validatedCreds = str_replace('\\\\n', '\\n', $creds);

$app->firebase = (new Factory)
	-> withServiceAccount(ServiceAccount::fromJson($validatedCreds))
	-> withDisabledAutoDiscovery()
	-> create();


/* Register Mongo DB */

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


/* Return Application Configurations */
return $app;
