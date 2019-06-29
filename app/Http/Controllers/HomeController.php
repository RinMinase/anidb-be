<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{

	public function index()
	{
		$data = [
			"status" => "200",
			"details" => [
				"id" => 1,
				"email" => "test@email.com",
				"mobile" => "123000123",
				"message" => "This is a test response"
			]
		];

		return response()->json($data);
	}

	public function query()
	{
		$data = app('firebase')->getDatabase()->getReference('hdd')->getValue();

		return response()->json($data);
	}

	public function test_database()
	{
		$data = app('firebase');

		return dd($data);
	}
}
