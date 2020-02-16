<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testExample()
	{
		$this->get('/');

		$app_version = explode(' ', $this->app->version())[1];
		$app_version = substr($app_version, 1, -1);

		$this->assertStringContainsString(
			$app_version, $this->response->getContent()
		);
	}
}
