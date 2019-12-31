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
					. '.nav {'
						. 'width: 100%;'
						. 'font-size: 18px;'
						. 'position: fixed;'
						. 'top: 0;'
						. 'display: flex;'
						. 'justify-content: space-between;'
						. 'padding: 10px;'
					. '}'
					. '.nav .item {'
						. 'margin: 0 10px;'
						. 'padding: 10px;'
						. 'font-weight: bold;'
					. '}'
					. '.nav .item:hover {'
						. 'background-color: #eee;'
						. 'border-radius: 8px;'
						. 'color: #222;'
					. '}'
					. '.nav .item a {'
						. 'color: inherit;'
						. 'text-decoration: none;'
					. '}'
					. '.title {text-align: center; font-size: 84px; margin: 0 8px;}'
					. '@media (max-width: 576px) { .title { font-size: 56px; } }'
					. '.subtitle {'
						. 'font-size: 18px;'
						. 'margin-bottom: 30px;'
						. 'position: fixed;'
						. 'text-align: center;'
						. 'bottom: 0;'
					. '}'
				. '</style>'
			. '</head><body>'
				. '<div class="nav">'
					. '<p class="item"><a href="/docs">Docs</a></p>'
					. '<p class="item">Source</p>'
				. '</div>'
				. '<div class="title">Rin\'s AniDB API / Middleware</div>'
				. '<div class="subtitle">Lumen Framework v' . $version . '</div>'
			. '</body></html>';
	}
}

if (! function_exists('generate_verification_email')) {
	function generate_verification_email($name, $verifyUrl) {
		if ($name) { $name = ' ' . $name; }

		return '<div style="background-color: rgb(245,249,250); padding: 25px 0; font-family: Roboto, Arial, Helvetica;">'
			. '<h1 style="text-align: center; color: #333">AniDB</h1>'
			. '<div style="background-color: #fff; width: 800px; margin: 0 auto; padding: 15px 40px 25px;">'
				. '<div style="color: #444">'
					. '<h2>Verify this Email Address</h2>'
					. '<p style="margin-top: 35px;">Hi' . $name . ',</p>'
					. '<p style="margin-top: 35px;">Welcome!</p>'
					. '<p>Please click the button below to verify your email address.</p>'
					. '<p>If you did not signup with AniDB, please ignore this email.</p>'

					. '<p style="margin-top: 40px;">Rin Minase<br>AniDB Creator</p>'
				. '</div>'

				. '<table style="margin: 0 auto;"><tbody><tr>'
					. '<td style="background-color: #ce636c; border-radius: 5px; padding: 8px 32px;">'
						. '<a href="' . $verifyUrl . '" target="_blank" style="text-decoration: none; color: #fff; outline: none;">Verify Email</a>'
					. '</td>'
				. '</tr></tbody></table>'
			. '</div>'
			. '<div style="text-align: center; margin-top: 15px;">'
				. '<table style="margin: 0 auto;"><tbody><tr>'
					. '<td style="background-color: #1a2052; border-radius: 5px; padding: 8px 24px;">'
						. '<a href="https://github.com/RinMinase/anidb" target="_blank" style="text-decoration: none; color: #fff; outline: none;">Github Repository</a>'
					. '</td>'
				. '</tr></tbody></table>'
			. '</div>'
		. '</div>';
	}
}

if (! function_exists('generate_verification_text')) {
	function generate_verification_text($name, $verifyUrl) {
		if ($name) { $name = ' ' . $name; }

		return 'Hi' . $name . '! Please copy the URL in your browser to verify your email address. ' . $verifyUrl;
	}
}
