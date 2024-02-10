<?php
require 'getenv.php';
use DevCoder\DotEnv;
(new DotEnv(__DIR__ . '/.env'))->load();

$api_key = $_ENV["VISUALCROSSING_API_KEY"];
$db_host = $_ENV["DB_HOST"];
$timezone = $_ENV["TIMEZONE"];
echo $api_key . "\n";
echo $db_host . "\n";
echo $timezone . "\n";
