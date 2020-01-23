<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');

$app = require __DIR__.'/../bootstrap/app.php';
$app->run();
