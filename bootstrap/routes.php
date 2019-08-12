<?php

$router->get('/', function() use ($router) {
	$version = explode(' ', $router->app->version())[1];
	$version = substr($version, 1, -1);

	return display_index_page($version);
});

$router->group(['prefix' => 'api'], function() use ($router) {
	$router->get('home', ['uses' => 'HomeController@index']);
	$router->get('mal[/{id}]', ['uses' => 'HomeController@mal']);
	$router->get('search[/{query}]', ['uses' => 'HomeController@mal_search']);
	// $router->get('query', ['uses' => 'HomeController@query']);
	$router->get('mongo', ['uses' => 'HomeController@mongo']);
});
