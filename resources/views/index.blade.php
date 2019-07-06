<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>Rin's AniDB API</title>

		<link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

		<style>
			body {
				margin: 0;
				color: #636b6f;
				font-family: 'Nunito', sans-serif;
				height: 100vh;
				align-items: center;
				display: flex;
				justify-content: center;
			}

			.title {
				text-align: center;
				font-size: 84px;
				margin: 0 8px;
			}

			@media (max-width: 576px) {
				.title {
					font-size: 56px;
				}
			}

			.subtitle {
				text-align: center;
				font-size: 18px;
				margin-bottom: 30px;
				position: fixed;
				bottom: 0;
			}
		</style>
	</head>
	<body>
		<div class="title">Rin's AniDB API / Middleware</div>
		<div class="subtitle">Lumen Framework v{{ $version }}</div>
	</body>
</html>
