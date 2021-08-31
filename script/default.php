<?php

declare(strict_types = 1);

error_reporting(E_ALL);

include dirname(__DIR__) . "/vendor/autoload.php";

define("BASE_DIR", dirname(__DIR__));

$kernel = new \App\HttpKernel();
$kernel->run();