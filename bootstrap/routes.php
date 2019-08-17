<?php

$router->get('/', function() use ($router) {
	$version = explode(' ', $router->app->version())[1];
	$version = substr($version, 1, -1);

	return display_index_page($version);
});

$router->group(['prefix' => 'api'], function() use ($router) {
	$router->get('mongo', ['uses' => 'HomeController@mongo']);

	$router->get('anime[/{params}]', ['uses' => 'AnimeController@retrieve']);
	$router->post('anime', ['uses' => 'AnimeController@create']);

	$router->get('download[/{params}]', ['uses' => 'DownloadController@retrieve']);

	$router->get('hdd[/{params}]', ['uses' => 'HddController@retrieve']);

	$router->get('summer[/{params}]', ['uses' => 'SummerController@retrieve']);
	$router->post('summer', ['uses' => 'SummerController@create']);
	$router->patch('summer/{params}', ['uses' => 'SummerController@update']);

	$router->get('img/{param:.*}', ['uses' => 'ImageController@retrieve']);
	$router->get('mal[/{params}]', ['uses' => 'MalController@queryMal']);

	$router->get('changelog[/{limit}]', ['uses' => 'ReleaseController@changelog']);
	$router->get('changelog-be[/{limit}]', ['uses' => 'ReleaseController@changelogBE']);
	$router->get('issues[/{limit}]', ['uses' => 'ReleaseController@issues']);
});
