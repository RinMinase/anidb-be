<?php

$router->get('/', function() use ($router) {
	$version = explode(' ', $router->app->version())[1];
	$version = substr($version, 1, -1);

	return display_index_page($version);
});

$router->group(['prefix' => 'api'], function() use ($router) {
	$router->get('mongo', ['uses' => 'HomeController@mongo']);

	$router->get('anime[/{params}]', ['uses' => 'AnimeController@retrieve']);
	$router->get('hdd[/{params}]', ['uses' => 'HddController@retrieve']);

	$router->get('mal[/{params}]', ['uses' => 'MalController@queryMal']);
});
