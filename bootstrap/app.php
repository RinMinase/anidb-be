<?php

require_once __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
	dirname(__DIR__)
))->bootstrap();


/*
 *--------------------------------------------------------------------------
 * Create The Application
 *--------------------------------------------------------------------------
 *
 * Here we will load the environment and create the application instance
 * that serves as the central piece of this framework. We'll use this
 * application as an "IoC" container and router for this framework.
 *
 */

$app = new Laravel\Lumen\Application(
	dirname(__DIR__)
);

// $app->withFacades();

// $app->withEloquent();


/*
 *--------------------------------------------------------------------------
 * Register Container Bindings
 *--------------------------------------------------------------------------
 *
 * Now we will register a few bindings in the service container. We will
 * register the exception handler and the console kernel. You may add
 * your own bindings here if you like or you can make another file.
 *
 */

$app->singleton(
	Illuminate\Contracts\Debug\ExceptionHandler::class,
	App\Exceptions\Handler::class
);

$app->singleton(
	Illuminate\Contracts\Console\Kernel::class,
	App\Console\Kernel::class
);


/*
 *--------------------------------------------------------------------------
 * Register Middleware
 *--------------------------------------------------------------------------
 *
 * Next, we will register the middleware with the application. These can
 * be global middleware that run before and after each request into a
 * route or middleware that'll be assigned to some specific routes.
 *
 */

// $app->middleware([
//     App\Http\Middleware\ExampleMiddleware::class
// ]);

// $app->routeMiddleware([
//     'auth' => App\Http\Middleware\Authenticate::class,
// ]);


/*
 *--------------------------------------------------------------------------
 * Register Service Providers
 *--------------------------------------------------------------------------
 *
 * Here we will register all of the application's service providers which
 * are used to bind services into the container. Service providers are
 * totally optional, so you are not required to uncomment this line.
 *
 */

// $app->register(App\Providers\AppServiceProvider::class);
// $app->register(App\Providers\AuthServiceProvider::class);
// $app->register(App\Providers\EventServiceProvider::class);


/*
 *--------------------------------------------------------------------------
 * Load The Application Routes
 *--------------------------------------------------------------------------
 *
 * Next we will include the routes file so that they can all be added to
 * the application. This will provide all of the URLs the application
 * can respond to, as well as the controllers that may handle them.
 *
 */

$app->router->group([
	'namespace' => 'App\Http\Controllers',
], function ($router) {
	require __DIR__.'/../routes/web.php';
});


/*
 * Register Firebase DB
 */

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

$creds = json_encode([
	"type" => "service_account",
	"project_id" => env('FIRE_PROJECT_ID', ''),
	"private_key_id" => env('FIRE_PRIVATE_KEY', ''),
	"private_key" => env('FIRE_KEY', ''),
	"client_email" => env('FIRE_EMAIL', ''),
	"client_id" => env('FIRE_CLIENT_ID', ''),
	"auth_uri" => "https://accounts.google.com/o/oauth2/auth",
	"token_uri" => "https://oauth2.googleapis.com/token",
	"auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
	"client_x509_cert_url" => env('FIRE_CERT_URL', ''),
]);

$validatedCreds = str_replace('\\\\n', '\\n', $creds);

$app->firebase = (new Factory)
	-> withServiceAccount(ServiceAccount::fromJson($validatedCreds))
	-> withDisabledAutoDiscovery()
	-> create();

/*
 * Register Mongo DB
 */

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

/*
 * Return Application Configurations
 */
return $app;
