<?php

declare(strict_types = 1);

error_reporting(E_ALL);

include dirname(__DIR__) . "/vendor/autoload.php";

$env = getenv("environ") ?: "dev";
$root = dirname(__DIR__);

$kernel = new \App\Http\Application($root, $env);
$kernel->run();
