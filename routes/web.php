<?php

$router->get('/', function() use ($router) {
	return $router->app->version();
});

$router->group(['prefix' => 'api'], function() use ($router) {
	$router->get('home', ['uses' => 'HomeController@index']);
	$router->get('query', ['uses' => 'HomeController@query']);
	$router->get('test', ['uses' => 'HomeController@test_database']);
});
