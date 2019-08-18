<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
}
