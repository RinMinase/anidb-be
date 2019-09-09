<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Mailgun\Mailgun;

class HomeController {

	public function mongo() {
		$data = app('mongo')->hdd->find();

		return response(mongo_json($data))->header('Content-Type', 'application/json');
	}

	public function export() {
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Test data');

		$writer = new Xlsx($spreadsheet);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="test.xlsx"');
		$writer->save('php://output');

		return $writer;
	}

	public function email() {
		if (!env('MAILGUN_TEST_USER')) {
			return response('Mailgun Test User configuration not present', 500);
		}

		$domain = 'sandbox' . env('MAILGUN_DOMAIN') . '.mailgun.org';
		$result = app('mail')->sendMessage(
			"$domain",
			[
				'from' => 'Mailgun Sandbox <anidb@sandbox' . env('MAILGUN_DOMAIN') . '.mailgun.org>',
				'to' => env('MAILGUN_TEST_USER'),
				'subject' => 'Greetings from AniDB!',
				'text' => generate_verification_text('User', 'testurl'),
				'html' => generate_verification_email('User', 'testurl'),
			]
		);

		return response()->json($result);
	}
}
