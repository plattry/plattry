<?php

return [
    "log" => [
        "driver" => "file",
        "level" => \Psr\Log\LogLevel::DEBUG,
        "date" => "Y-m-d H:i:s.u",
        "size" => 0,
        "path" => "runtime/log",
        "split" => \Plattry\Kit\Log\Driver\FileDriver::SPLIT_MONTH
    ]
];
