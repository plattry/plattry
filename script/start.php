<?php

require_once "./vendor/autoload.php";

$env = getenv("SPACE") ?: \Bootstrap\LoadSpace::DEVELOPMENT->value;

$kernel = (new \Bootstrap\Kernel());
$kernel->run(\Bootstrap\LoadSpace::from($env), \Bootstrap\LoadMode::HTTP);
