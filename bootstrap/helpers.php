<?php

if (! function_exists('mongo_json')) {
	function mongo_json($query_result) {
		$data = [];

		foreach($query_result as $k => $row) {
			$data[] = $row;
		}

		return json_encode($data);
	}
}

if (! function_exists('trim_dom_crawler')) {
	function trim_dom_crawler($input) {
		return trim(str_replace($input->text(), '', $input->parents()->text()));
	}
}

if (! function_exists('random_string')) {
	function random_string($length = 32) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';

		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}

		return $randomString;
	}
}

if (! function_exists('display_index_page')) {
	function display_index_page($version) {
		return '<html lang="en"><head>'
				. '<meta name="viewport" content="width=device-width, initial-scale=1">'
				. '<link href="https://fonts.googleapis.com/css?family=Nunito:200" rel="stylesheet" type="text/css">'
				. '<link rel="icon" href="data:;base64,=">'
				. '<title>Rin\'s AniDB API</title>'
				. '<style>'
					. 'body {'
						. 'margin: 0;'
						. 'color: #636b6f;'
						. 'font-family: "Nunito", sans-serif;'
						. 'height: 100vh;'
						. 'display: flex;'
						. 'align-items: center;'
						. 'justify-content: center;'
					. '}'
					. '.title {text-align: center; font-size: 84px; margin: 0 8px;}'
					. '@media (max-width: 576px) { .title { font-size: 56px; } }'
					. '.subtitle {'
						. 'font-size: 18px;'
						. 'margin-bottom: 30px;'
						. 'position: fixed;'
						. 'bottom: 0;'
					. '}'
				. '</style>'
			. '</head><body>'
				. '<div class="title">Rin\'s AniDB API / Middleware</div>'
				. '<div class="subtitle">Lumen Framework v' . $version . '</div>'
			. '</body></html>';
	}
}
