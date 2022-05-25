<?php

declare(strict_types = 1);

error_reporting(E_ALL);

include "./vendor/autoload.php";

// Create an application instance and run it.
(new \App\Http\Application(dirname(__DIR__), "dev"))->run();
