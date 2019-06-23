<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller {
	public function index() {
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
}
