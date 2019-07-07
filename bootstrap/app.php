<?php

require_once __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(dirname(__DIR__)))->bootstrap();

/* Create The Application */

$app = new Laravel\Lumen\Application(dirname(__DIR__));
// $app->withFacades();
// $app->withEloquent();


/* Register Container Bindings */

$app->singleton(
	Illuminate\Contracts\Debug\ExceptionHandler::class,
	App\Middleware\ExceptionsHandler::class
);


/* Register Middleware */

// $app->routeMiddleware([
//     'auth' => App\Middleware\Authenticate::class,
// ]);


/* Load The Application Routes */

$app->router->group([
	'namespace' => 'App\Controllers',
], function ($router) { require __DIR__.'/../app/routes/web.php'; });


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
